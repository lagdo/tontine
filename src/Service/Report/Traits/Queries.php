<?php

namespace Siak\Tontine\Service\Report\Traits;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

trait Queries
{
    /**
     * @return Builder
     */
    private function getRefundQuery(): Builder
    {
        /*
         * How it is calculated.
         * When a debt is refuunded in a given session, the corresponding amount is
         * the debt initial amount, minus the sum of partial refunds for that debt.
         */
        $debtPartialRefund = DB::table('partial_refunds')
            ->select(DB::raw('sum(partial_refunds.amount)'))
            ->whereColumn('partial_refunds.debt_id', 'debts.id')
            ->toSql();
        // PostgreSQL aggregate functions return null when applied to empty resultsets,
        // making some other operations (like addition with the aggregate value) also null.
        // We then need to explicitely convert null values of aggregate functions to 0.
        $debtPartialRefund = DB::connection()->getDriverName() === 'pgsql' ?
            "coalesce(($debtPartialRefund),0)" : "($debtPartialRefund)";
        return DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->select(DB::raw("sum(debts.amount - $debtPartialRefund) as total_amount"));
    }
}
