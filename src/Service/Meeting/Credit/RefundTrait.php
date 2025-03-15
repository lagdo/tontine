<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function tap;

trait RefundTrait
{
    /**
     * @var TenantService
     */
    private TenantService $tenantService;

    /**
     * @var FundService
     */
    private FundService $fundService;

    /**
     * @param Session $session The session
     * @param Fund $fund
     * @param bool|null $onlyPaid
     * @param bool $with
     *
     * @return Builder|Relation
     */
    private function getDebtsQuery(Session $session, Fund $fund,
        ?bool $onlyPaid, bool $with): Builder|Relation
    {
        $prevSessions = $this->fundService->getFundSessionIds($session, $fund)
            ->filter(fn(int $sessionId) => $sessionId !== $session->id);

        return Debt::whereHas('loan', function(Builder $query) use($fund) {
                $query->where('fund_id', $fund->id);
            })
            ->when($onlyPaid === false, function(Builder $query) {
                return $query->whereDoesntHave('refund');
            })
            ->when($onlyPaid === true, function(Builder $query) {
                return $query->whereHas('refund');
            })
            ->where(function(Builder $query) use($session, $prevSessions) {
                // Take all the debts in the current session
                $query->where(function(Builder $query) use($session) {
                    $query->whereHas('loan', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                });
                if($prevSessions->count() === 0)
                {
                    return;
                }
                // The debts in the previous sessions.
                $query->orWhere(function(Builder $query) use($session, $prevSessions) {
                    $query->whereHas('loan', function(Builder $query) use($prevSessions) {
                        $query->whereIn('session_id', $prevSessions);
                    })
                    ->where(function(Builder $query) use($session) {
                        // The debts that are not yet refunded.
                        $query->orWhereDoesntHave('refund');
                        // The debts that are refunded in the current session.
                        $query->orWhereHas('refund', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
                });
            })
            ->when($with, function(Builder $query) use($session) {
                $query->with([
                    'loan.member',
                    'loan.session',
                    'refund.session',
                    'partial_refunds.session',
                    'partial_refund' => fn($q) => $q->where('session_id', $session->id),
                ]);
            });
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    private function canCreateRefund(Debt $debt, Session $session): bool
    {
        // Already refunded
        // Cannot refund the principal debt in the same session as the loan.
        if(!$session->opened || $debt->refund !== null ||
            $debt->is_principal && $debt->loan->session->id === $session->id)
        {
            return false;
        }

        // Cannot refund a recurrent interest debt before the principal.
        if($debt->is_interest && $debt->loan->recurrent_interest)
        {
            return $debt->loan->principal_debt->refund !== null;
        }

        // Cannot refund the principal debt before the last partial refund.
        $lastRefund = $debt->partial_refunds->sortByDesc('session.start_at')->first();
        return !$lastRefund || $lastRefund->session->start_at < $session->start_at;
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    private function canDeleteRefund(Debt $debt, Session $session): bool
    {
        // A refund can only be deleted in the same session it was created.
        if(!$session->opened || !$debt->refund || $debt->refund->session_id !== $session->id)
        {
            return false;
        }
        // Cannot delete the principal refund if the interest is also refunded.
        if($debt->is_principal && $debt->loan->recurrent_interest)
        {
            return $debt->loan->interest_debt->refund === null;
        }

        return true;
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    private function canCreatePartialRefund(Debt $debt, Session $session): bool
    {
        // Cannot refund the principal debt in the same session.
        if(!$session->opened || $debt->refund !== null ||
            ($debt->is_principal && $debt->loan->session->id === $session->id))
        {
            return false;
        }

        return true;
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return void
     */
    private function fillDebt(Debt $debt, Session $session): void
    {
        $debt->isEditable = $debt->refund !== null ?
            $this->canDeleteRefund($debt, $session) :
            $this->canCreateRefund($debt, $session);
        $debt->canPartiallyRefund = $this->canCreatePartialRefund($debt, $session);
    }

    /**
     * @param int $debtId
     *
     * @return Debt|null
     */
    public function getDebt(int $debtId): ?Debt
    {
        return Debt::whereHas('loan',
            fn(Builder|Relation $loanQuery) => $loanQuery->whereHas('member',
                fn(Builder|Relation $memberQuery) => $memberQuery->where('tontine_id',
                    $this->tenantService->tontine()->id)))
            ->find($debtId);
    }

    /**
     * @param Session $session The session
     * @param Fund $fund
     * @param int $debtId
     *
     * @return Debt|null
     */
    public function getFundDebt(Session $session, Fund $fund, int $debtId): ?Debt
    {
        return tap($this->getDebtsQuery($session, $fund, null, true)->find($debtId),
            fn(Debt $debt) => $debt !== null && $this->fillDebt($debt, $session));
    }
}
