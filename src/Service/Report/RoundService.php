<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;

class RoundService
{
    use Traits\Queries;

    /**
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getSettlementAmounts(Collection $sessionIds): Collection
    {
        return DB::table('settlements')
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->select(DB::raw('sum(bills.amount) as total_amount'), 'settlements.session_id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->groupBy('settlements.session_id')
            ->pluck('total_amount', 'session_id');
    }

    /**
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getLoanAmounts(Collection $sessionIds): Collection
    {
        return DB::table('debts')
            ->join('loans', 'loans.id', '=', 'debts.loan_id')
            ->select(DB::raw('sum(debts.amount) as total_amount'), 'loans.session_id')
            ->where('debts.type', Debt::TYPE_PRINCIPAL)
            ->whereIn('loans.session_id', $sessionIds)
            ->groupBy('loans.session_id')
            ->pluck('total_amount', 'session_id');
    }

    /**
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getRefundAmounts(Collection $sessionIds): Collection
    {
        $refunds = $this->getRefundQuery()
            ->addSelect('refunds.session_id')
            ->groupBy('refunds.session_id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->pluck('total_amount', 'session_id');
        DB::table('partial_refunds')
            ->whereIn('session_id', $sessionIds)
            ->select('session_id', DB::raw('sum(amount) as total_amount'))
            ->groupBy('session_id')
            ->get()
            // Merge into refunds
            ->each(function($partialRefund) use($refunds) {
                $refunds[$partialRefund->session_id] = $partialRefund->total_amount +
                    ($refunds[$partialRefund->session_id] ?? 0);
            });

        return $refunds;
    }

    /**
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getAuctionAmounts(Collection $sessionIds): Collection
    {
        return DB::table('auctions')
            ->select(DB::raw('sum(amount) as total_amount'), 'session_id')
            ->where('paid', true)
            ->whereIn('session_id', $sessionIds)
            ->groupBy('session_id')
            ->pluck('total_amount', 'session_id');
    }

    /**
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getSavingAmounts(Collection $sessionIds): Collection
    {
        return DB::table('savings')
            ->select(DB::raw('sum(amount) as total_amount'), 'session_id')
            ->whereIn('session_id', $sessionIds)
            ->groupBy('session_id')
            ->pluck('total_amount', 'session_id');
    }

    /**
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getDisbursementAmounts(Collection $sessionIds): Collection
    {
        return DB::table('disbursements')
            ->select(DB::raw('sum(amount) as total_amount'), 'session_id')
            ->whereIn('session_id', $sessionIds)
            ->groupBy('session_id')
            ->pluck('total_amount', 'session_id');
    }
}
