<?php

namespace Siak\Tontine\Service;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Disbursement;
use Siak\Tontine\Model\Funding;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\TenantService;

class BalanceCalculator
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function auctionAmount(Collection $sessionIds)
    {
        return Auction::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->where('paid', true)
            ->value('total');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function fundingAmount(Collection $sessionIds)
    {
        return Funding::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->value('total');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function settlementAmount(Collection $sessionIds)
    {
        return Settlement::select(DB::raw('sum(bills.amount) as total'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->value('total');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function refundAmount(Collection $sessionIds)
    {
        return Refund::select(DB::raw('sum(debts.amount) as total'))
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->value('total')
        // Partial refunds on debts that are not yet refunded.
        + PartialRefund::select(DB::raw('sum(partial_refunds.amount) as total'))
            ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
            ->whereIn('partial_refunds.session_id', $sessionIds)
            ->whereNotExists(function (Builder $query) {
                $query->select(DB::raw(1))->from('refunds')
                    ->whereColumn('refunds.debt_id', 'debts.id');
            })
            ->value('total');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function debtAmount(Collection $sessionIds)
    {
        return Debt::principal()->select(DB::raw('sum(debts.amount) as total'))
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('loans.session_id', $sessionIds)
            ->value('total');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function disbursementAmount(Collection $sessionIds)
    {
        return Disbursement::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->value('total');
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getBalanceForLoan(Session $session): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->getPreviousSessions($session);

        // The amount available for lending is the sum of the auctions, fundings,
        // settlements and refunds, minus the sum of the loans and disbursements,
        // for all the sessions until the selected.

        return $this->auctionAmount($sessionIds) + $this->fundingAmount($sessionIds) +
            $this->settlementAmount($sessionIds) + $this->refundAmount($sessionIds) -
            $this->debtAmount($sessionIds) - $this->disbursementAmount($sessionIds);
    }

    /**
     * Get the amount available for disbursement.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getBalanceForDisbursement(Session $session): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->getPreviousSessions($session);

        // The amount available for disbursement is the sum of the auctions,
        // settlements and refunds, minus the sum of the loans and disbursements,
        // for all the sessions until the selected.

        return $this->auctionAmount($sessionIds) + $this->settlementAmount($sessionIds) +
            $this->refundAmount($sessionIds) - $this->debtAmount($sessionIds) -
            $this->disbursementAmount($sessionIds);
    }
}
