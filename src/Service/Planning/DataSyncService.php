<?php

namespace Siak\Tontine\Service\Planning;

use Closure;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;

use function trans;

class DataSyncService
{
    /**
     * @param string $table
     * @param string $relation
     *
     * @return Closure
     */
    private function filter(string $table, string $relation): Closure
    {
        return function($query) use($relation, $table) {
            $query->select(DB::raw(1))
                ->from($relation)
                ->whereColumn("$relation.session_id", "$table.session_id")
                ->whereColumn("$relation.pool_id", 'subscriptions.pool_id');
        };
    }

    /**
     * @param string $table
     *
     * @return Closure
     */
    private function filters(string $table): Closure
    {
        return function($query) use($table) {
            $query->orWhereNotExists($this->filter($table, 'v_pool_session'))
                ->orWhereExists($this->filter($table, 'pool_session_disabled'));
        };
    }

    /**
     * @param Pool $pool
     * @param bool $filter
     *
     * @return void
     * @throws MessageException
     */
    public function syncPool(Pool $pool, bool $filter): void
    {
        // Check for existing remitments.
        $payables = DB::table('payables')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filter, fn($query) => $query->where($this->filters('payables')))
            ->select('payables.id')
            ->distinct()
            ->pluck('id');
        if($payables->count() > 0 &&
            DB::table('remitments')->whereIn('payable_id', $payables)->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.payments'));
        }
        // Check for existing deposits.
        $receivables = DB::table('receivables')
            ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filter, fn($query) => $query->where($this->filters('receivables')))
            ->select('receivables.id')
            ->distinct()
            ->pluck('id');
        if($receivables->count() > 0 &&
            DB::table('deposits')->whereIn('receivable_id', $receivables)->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.payments'));
        }
        // Detach the payables from their sessions.
        if($payables->count() > 0)
        {
            DB::table('payables')->whereIn('id', $payables)->update(['session_id' => null]);
        }
        // Delete the receivables.
        if($receivables->count() > 0)
        {
            DB::table('receivables')->whereIn('id', $receivables)->delete();
        }
    }
}
