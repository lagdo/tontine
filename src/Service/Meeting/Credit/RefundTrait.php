<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\TenantService;

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
     * @param Session $session
     * @param Fund|null $fund
     * @param bool|null $onlyPaid
     * @param bool $with
     *
     * @return Builder|Relation
     */
    private function getDebtsQuery(Session $session, ?Fund $fund,
        ?bool $onlyPaid, bool $with): Builder|Relation
    {
        return Debt::select(['debts.*', DB::raw('member_defs.name as member')])
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->join('members', 'loans.member_id', '=', 'members.id')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->join('sessions', 'loans.session_id', '=', 'sessions.id')
            ->when($fund !== null, fn(Builder $ql) => $ql->where('loans.fund_id', $fund->id))
            ->whereIn('loans.fund_id', DB::table('v_fund_session')
                ->select('fund_id')->where('session_id', $session->id))
            ->where('sessions.day_date', '<=', $session->day_date)
            ->where(fn(Builder $query) => $query
                // The debts that are not yet refunded.
                ->orWhereDoesntHave('refund')
                // The debts that are refunded in or after the current session.
                ->orWhereHas('refund', fn(Builder $q) => $q->whereHas('session',
                    fn(Builder $qs) => $qs->where('day_date', '>=', $session->day_date))))
            ->when($onlyPaid === false, fn(Builder $q) => $q->whereDoesntHave('refund'))
            ->when($onlyPaid === true, fn(Builder $q) => $q->whereHas('refund'))
            ->when($with, fn(Builder $query) => $query->with([
                'loan.session',
                'refund.session',
                'partial_refunds.session',
                'partial_refund' => fn($q) => $q->where('session_id', $session->id),
                'loan.fund.sessions' => fn($q) => $q->select(['id', 'day_date']),
                'loan.fund.interest',
            ]));
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
            ($debt->is_principal && $debt->loan->session->id === $session->id))
        {
            return false;
        }

        // Cannot refund a recurrent interest debt before the principal.
        if($debt->is_interest && $debt->loan->recurrent_interest)
        {
            $refund = $debt->loan->principal_debt->refund;
            return $refund !== null && $refund->session->day_date <= $session->day_date;
        }

        // Cannot refund a debt before the last partial refund.
        $lastRefund = $debt->partial_refunds->sortByDesc('session.day_date')->first();
        return !$lastRefund || $lastRefund->session->day_date < $session->day_date;
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
     * @return bool
     */
    private function isEditable(Debt $debt, Session $session): bool
    {
        return $debt->refund !== null ?
            $this->canDeleteRefund($debt, $session) :
            $this->canCreateRefund($debt, $session);
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return void
     */
    private function fillDebt(Debt $debt, Session $session): void
    {
        $debt->isEditable = $this->isEditable($debt, $session);
        $debt->canPartiallyRefund = $this->canCreatePartialRefund($debt, $session);
    }

    /**
     * @param Session $session
     * @param int $debtId
     *
     * @return Debt|null
     */
    public function getDebt(Session $session, int $debtId): ?Debt
    {
        return Debt::whereHas('loan', fn($ql) =>
                $ql->whereIn('fund_id', $session->funds()->pluck('id')))
            ->find($debtId);
    }

    /**
     * @param Session $session
     * @param Fund|null $fund
     * @param int $debtId
     *
     * @return Debt|null
     */
    public function getFundDebt(Session $session, ?Fund $fund, int $debtId): ?Debt
    {
        return tap($this->getDebtsQuery($session, $fund, null, true)->find($debtId),
            fn(?Debt $debt) => $debt !== null && $this->fillDebt($debt, $session));
    }
}
