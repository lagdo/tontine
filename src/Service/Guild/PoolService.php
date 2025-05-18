<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Guild;
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
     * @param Guild $guild
     * @param int $page
     *
     * @return Collection
     */
    public function getPools(Guild $guild, int $page = 0): Collection
    {
        return $guild->pools()
            ->orderBy('id')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of pools.
     *
     * @param Guild $guild
     *
     * @return int
     */
    public function getPoolCount(Guild $guild): int
    {
        return $guild->pools()->count();
    }

    /**
     * Get a single pool.
     *
     * @param Guild $guild
     * @param int $poolId
     *
     * @return PoolDef|null
     */
    public function getPool(Guild $guild, int $poolId): ?PoolDef
    {
        return $guild->pools()->withCount('pools')->find($poolId);
    }

    /**
     * Add a new pool.
     *
     * @param Guild $guild
     * @param array $values
     *
     * @return bool
     */
    public function createPool(Guild $guild, array $values): bool
    {
        $guild->pools()->create($values);
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
     * @param Guild $guild
     * @param int $count
     *
     * @return Collection
     */
    public function getFakePools(Guild $guild, int $count): Collection
    {
        return PoolDef::factory()->count($count)->make(['guild_id' => $guild]);
    }
}
