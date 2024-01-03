<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;

use function trans;

class RefundService
{
    /**
     * @param DebtCalculator $debtCalculator
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     */
    public function __construct(private DebtCalculator $debtCalculator,
        private TenantService $tenantService, private LocaleService $localeService,
        private SessionService $sessionService)
    {}

    /**
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return Builder|Relation
     */
    private function getQuery(Session $session, ?bool $onlyPaid = null): Builder|Relation
    {
        $sessionId = $session->id;
        $prevSessions = $this->sessionService->getTontineSessionIds($session, withCurr: false);

        return Debt::when($onlyPaid === false, function(Builder $query) {
                return $query->whereDoesntHave('refund');
            })
            ->when($onlyPaid === true, function(Builder $query) {
                return $query->whereHas('refund');
            })
            ->where(function(Builder $query) use($sessionId, $prevSessions) {
                // Take all the debts in the current session
                $query->where(function(Builder $query) use($sessionId) {
                    $query->whereHas('loan', function(Builder $query) use($sessionId) {
                        $query->where('session_id', $sessionId);
                    });
                });
                if($prevSessions->count() === 0)
                {
                    return;
                }
                // The debts in the previous sessions.
                $query->orWhere(function(Builder $query) use($sessionId, $prevSessions) {
                    $query->whereHas('loan', function(Builder $query) use($prevSessions) {
                        $query->whereIn('session_id', $prevSessions);
                    })
                    ->where(function(Builder $query) use($sessionId) {
                        // The debts that are not yet refunded.
                        $query->orWhereDoesntHave('refund');
                        // The debts that are refunded in the current session.
                        $query->orWhereHas('refund', function(Builder $query) use($sessionId) {
                            $query->where('session_id', $sessionId);
                        });
                    });
                });
            });
    }

    /**
     * Get the number of debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return int
     */
    public function getDebtCount(Session $session, ?bool $onlyPaid): int
    {
        return $this->getQuery($session, $onlyPaid)->count();
    }

    /**
     * Get the debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getDebts(Session $session, ?bool $onlyPaid, int $page = 0): Collection
    {
        return $this->getQuery($session, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['loan', 'loan.member', 'loan.session', 'refund', 'partial_refunds.session'])
            ->get()
            ->sortBy('loan.member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * Get the refunds for a given session.
     *
     * @param Session $session The session
     * @param int $page
     *
     * @return Collection
     */
    public function getRefunds(Session $session, int $page = 0): Collection
    {
        return $session->refunds()
            ->page($page, $this->tenantService->getLimit())
            ->with('debt.loan.member')
            ->get();
    }

    /**
     * @param Session $session The session
     * @param int $debtId
     *
     * @return Debt|null
     */
    private function getDebt(Session $session, int $debtId): ?Debt
    {
        return $this->getQuery($session)->find($debtId);
    }

    /**
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function createRefund(Session $session, int $debtId): void
    {
        $debt = $this->getDebt($session, $debtId);
        if(!$debt || $debt->refund)
        {
            throw new MessageException(trans('tontine.loan.errors.not_found'));
        }
        if(!$this->debtCalculator->debtIsEditable($session, $debt))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_refund'));
        }

        $refund = new Refund();
        $refund->debt()->associate($debt);
        $refund->session()->associate($session);
        DB::transaction(function() use($session, $debt, $refund) {
            $refund->save();
            // For simple or compound interest, also save the final amount.
            if($debt->is_interest && !$debt->loan->fixed_interest)
            {
                $debt->amount = $this->debtCalculator->getDebtAmount($session, $debt);
                $debt->save();
            }
        });
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function deleteRefund(Session $session, int $debtId): void
    {
        $refund = Refund::with('debt')->where('session_id', $session->id)
            ->where('debt_id', $debtId)->first();
        if(!$refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }
        if((!$refund->editable))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        if(!$this->debtCalculator->debtIsEditable($session, $refund->debt))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_refund'));
        }
        $refund->delete();
    }

    /**
     * Get the number of partial refunds.
     *
     * @param Session $session The session
     *
     * @return int
     */
    public function getPartialRefundCount(Session $session): int
    {
        return $session->partial_refunds()->count();
    }

    /**
     * Get the partial refunds.
     *
     * @param Session $session The session
     * @param int $page
     *
     * @return Collection
     */
    public function getPartialRefunds(Session $session, int $page = 0): Collection
    {
        return $session->partial_refunds()
            ->page($page, $this->tenantService->getLimit())
            ->with(['debt.refund', 'debt.loan.member', 'debt.loan.session'])
            ->get()
            ->sortBy('debt.loan.member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * Get debt list for dropdown.
     *
     * @param Session $session The session
     *
     * @return Collection
     */
    public function getUnpaidDebtList(Session $session): Collection
    {
        return $this->getDebts($session, false)
            ->filter(function($debt) use($session) {
                return $this->debtCalculator->debtIsEditable($session, $debt) &&
                    $this->debtCalculator->getDebtAmount($session, $debt) > 0;
            })
            ->keyBy('id')
            ->map(function($debt) use($session) {
                $amount = $this->debtCalculator->getDebtDueAmount($session, $debt);

                return $debt->loan->member->name . ' - ' . $debt->loan->session->title .
                    ' - ' . trans('meeting.loan.labels.' . $debt->type) .
                    ' - ' . $this->localeService->formatMoney($amount, true);
            });
    }

    /**
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     * @param int $amount
     *
     * @return void
     */
    public function createPartialRefund(Session $session, int $debtId, int $amount): void
    {
        $debt = $this->getDebt($session, $debtId);
        if(!$debt || $debt->refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }
        // A partial refund must not totally refund a debt
        if($amount >= $debt->due_amount)
        {
            throw new MessageException(trans('meeting.refund.errors.pr_amount'));
        }

        $refund = new PartialRefund();
        $refund->amount = $amount;
        $refund->debt()->associate($debt);
        $refund->session()->associate($session);
        $refund->save();
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $refundId
     *
     * @return void
     */
    public function deletePartialRefund(Session $session, int $refundId): void
    {
        $refund = PartialRefund::where('session_id', $session->id)
            ->with(['debt.refund'])->find($refundId);
        if(!$refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }
        if($refund->debt->refund !== null || !$refund->editable)
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        $refund->delete();
    }
}
