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
        // The real amount refunded for a debt in a given session is
        // the debt initial amount, minus the sum of partial amounts for that debt.
        $debtPartialRefund = DB::table('partial_refunds')
            ->select(DB::raw('sum(partial_refunds.amount)'))
            ->whereColumn('partial_refunds.debt_id', 'debts.id')
            ->toSql();
        // PostgreSQL aggregate functions return null when they apply to empty resultsets,
        // making some other operations (like addition with the aggregate value) also null.
        // We then need to explicitely convert null values for aggregate functions to 0.
        $debtPartialRefund = DB::connection()->getDriverName() === 'pgsql' ?
            "coalesce(($debtPartialRefund),0)" : "($debtPartialRefund)";
        $refunds = DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->select('refunds.session_id',
                DB::raw("sum(debts.amount - $debtPartialRefund) as total_amount"))
            ->whereIn('refunds.session_id', $sessionIds)
            ->groupBy('refunds.session_id')
            ->pluck('total_amount', 'session_id');
        DB::table('partial_refunds')
            ->select('partial_refunds.session_id',
                DB::raw('sum(partial_refunds.amount) as total_amount'))
            ->whereIn('partial_refunds.session_id', $sessionIds)
            ->groupBy('partial_refunds.session_id')
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
