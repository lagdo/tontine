<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;

class RoundService
{
    /**
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getSettlementAmounts(Collection $sessionIds): Collection
    {
        return DB::table('bills')
            ->join('settlements', 'settlements.bill_id', '=', 'bills.id')
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
        return DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->select(DB::raw('sum(debts.amount) as total_amount'), 'refunds.session_id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->groupBy('refunds.session_id')
            ->pluck('total_amount', 'session_id');
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
    public function getFundingAmounts(Collection $sessionIds): Collection
    {
        return DB::table('fundings')
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
