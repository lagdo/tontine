<?php

namespace Siak\Tontine\Service\Planning;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\PoolTrait;

use function trans;

class PoolService
{
    use PoolTrait;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * @return Builder
     */
    private function getQuery(): Builder
    {
        $round = $this->tenantService->round();
        return $this->getPoolsQuery($round, $round->start_at, $round->end_at);
    }

    /**
     * Get a paginated list of pools in the selected round.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getPools(int $page = 0): Collection
    {
        return $this->getQuery()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of pools in the selected round.
     *
     * @return int
     */
    public function getPoolCount(): int
    {
        return $this->getQuery()->count();
    }

    /**
     * Get a single pool.
     *
     * @param int $poolId    The pool id
     *
     * @return Pool|null
     */
    public function getPool(int $poolId): ?Pool
    {
        return $this->tenantService->getPool($poolId);
    }

    /**
     * Add a new pool.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createPool(array $values): bool
    {
        $this->tenantService->round()->pools()->create($values);

        return true;
    }

    /**
     * Update a pool.
     *
     * @param Pool $pool
     * @param array $values
     *
     * @return bool
     */
    public function updatePool(Pool $pool, array $values): bool
    {
        return $pool->update($values);
    }

    /**
     * Delete a pool.
     *
     * @param Pool $pool
     *
     * @return void
     */
    public function deletePool(Pool $pool)
    {
        try
        {
            DB::transaction(function() use($pool) {
                DB::table('pool_session_disabled')->where('pool_id', $pool->id)->delete();
                $subscriptionIds = $pool->subscriptions()->pluck('id');
                DB::table('receivables')->whereIn('subscription_id', $subscriptionIds)->delete();
                DB::table('payables')->whereIn('subscription_id', $subscriptionIds)->delete();
                $pool->subscriptions()->delete();
                $pool->delete();
            });
        }
        catch(Exception $e)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.subscription'));
        }
    }

    /**
     * @param int $count
     *
     * @return Collection
     */
    public function getFakePools(int $count): Collection
    {
        return Pool::factory()->count($count)->make([
            'round_id' => $this->tenantService->round(),
        ]);
    }
}
