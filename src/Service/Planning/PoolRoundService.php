<?php

namespace Siak\Tontine\Service\Planning;


use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class PoolRoundService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a single pool.
     *
     * @param int $poolId    The pool id
     *
     * @return Pool|null
     */
    public function getPool(int $poolId): ?Pool
    {
        return $this->tenantService->tontine()->pools()
            ->with(['pool_round.start_session', 'pool_round.end_session'])
            ->find($poolId);
    }

    /**
     * Get a paginated list of pools in the selected round.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getSessions(int $page = 0): Collection
    {
        return $this->tenantService->tontine()->sessions()
            ->orderByDesc('start_at')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of pools in the selected round.
     *
     * @return int
     */
    public function getSessionCount(): int
    {
        return $this->tenantService->tontine()->sessions()->count();
    }

    /**
     * Get a paginated list of pools in the selected round.
     *
     * @param int $sessionId
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->tontine()->sessions()->find($sessionId);
    }

    /**
     * Save the pool round.
     *
     * @param Pool $pool
     * @param array $values
     *
     * @return void
     */
    public function saveRound(Pool $pool, array $values)
    {
        $pool->pool_round()->updateOrCreate([], $values);
    }

    /**
     * Delete the pool round.
     *
     * @param Pool $pool
     *
     * @return void
     */
    public function deleteRound(Pool $pool)
    {
        $pool->pool_round()->delete();
    }
}
