<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\PoolDef;
use Siak\Tontine\Service\TenantService;

class PoolService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
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
            ->orderBy('id')
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
     * @return PoolDef|null
     */
    public function getPool(int $poolId): ?PoolDef
    {
        return $this->tenantService->guild()->pools()->withCount('pools')->find($poolId);
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
     * @param PoolDef $pool
     * @param array $values
     *
     * @return bool
     */
    public function updatePool(PoolDef $pool, array $values): bool
    {
        return $pool->update($values);
    }

    /**
     * Toggle a pool.
     *
     * @param PoolDef $pool
     *
     * @return void
     */
    public function togglePool(PoolDef $pool)
    {
        $pool->update(['active' => !$pool->active]);
    }

    /**
     * Delete a pool.
     *
     * @param PoolDef $pool
     *
     * @return void
     */
    public function deletePool(PoolDef $pool)
    {
        $pool->delete();
    }

    /**
     * @param int $count
     *
     * @return Collection
     */
    public function getFakePools(int $count): Collection
    {
        return PoolDef::factory()->count($count)->make([
            'guild_id' => $this->tenantService->guild(),
        ]);
    }
}
