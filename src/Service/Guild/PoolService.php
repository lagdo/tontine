<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class PoolService
{
    /**
     * @param TenantService $tenantService
     * @param DataSyncService $dataSyncService
     */
    public function __construct(protected TenantService $tenantService,
        private DataSyncService $dataSyncService)
    {}

    /**
     * Get a paginated list of pools.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getPools(int $page = 0): Collection
    {
        return $this->tenantService->guild()->pools()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of pools.
     *
     * @return int
     */
    public function getPoolCount(): int
    {
        return $this->tenantService->guild()->pools()->count();
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
        return $this->tenantService->guild()->pools()->find($poolId);
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
        $this->tenantService->guild()->pools()->create($values);

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
        DB::transaction(function() use($pool) {
            $this->dataSyncService->syncPool($pool, false);

            $pool->pool_round()->delete();
            $pool->subscriptions()->delete();
            $pool->delete();
        });
    }

    /**
     * @param int $count
     *
     * @return Collection
     */
    public function getFakePools(int $count): Collection
    {
        return Pool::factory()->count($count)->make([
            'guild_id' => $this->tenantService->guild(),
        ]);
    }
}
