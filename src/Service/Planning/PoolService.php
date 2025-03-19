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
     * @return Builder|Relation
     */
    private function getQuery(): Builder|Relation
    {
        return Pool::ofRound($this->tenantService->round());
    }

    /**
     * Get a paginated list of pools.
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
     * Get the number of pools.
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
        return $this->getQuery()->with('counter')->find($poolId);
    }

    /**
     * Get the pools in the current round.
     *
     * @return Collection
     */
    public function getRoundPools(): Collection
    {
        return $this->tenantService->round()->pools()->get();
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
            'round_id' => $this->tenantService->round(),
        ]);
    }

    /**
     * Save the pool start and/or end session.
     *
     * @param Pool $pool
     * @param array $values
     *
     * @return void
     */
    private function _saveSessions(Pool $pool, array $values)
    {
        if($pool->pool_round !== null)
        {
            $pool->pool_round()->update($values);
            return;
        }

        // The initial value is the same for start and end sessions.
        if(!isset($values['start_session_id']))
        {
            $values['start_session_id'] = $this
                ->getPoolStartSession($pool)?->id ?? $values['end_session_id'];
        }
        if(!isset($values['end_session_id']))
        {
            $values['end_session_id'] = $this
                ->getPoolEndSession($pool)?->id ?? $values['start_session_id'];
        }
        $pool->pool_round()->create($values);
    }

    /**
     * Save the pool start and/or end session.
     *
     * @param Pool $pool
     * @param array $values
     *
     * @return void
     */
    public function saveSessions(Pool $pool, array $values)
    {
        DB::transaction(function() use($pool, $values) {
            $this->_saveSessions($pool, $values);

            $this->dataSyncService->syncPool($pool, true);
        });
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
        DB::transaction(function() use($pool) {
            $pool->pool_round()->delete();

            $this->dataSyncService->syncPool($pool, true);
        });
    }

    /**
     * Get the first session of the pool.
     *
     * @param Pool $pool
     *
     * @return Session|null
     */
    public function getPoolStartSession(Pool $pool): ?Session
    {
        return $pool->pool_round?->start_session ??
            $pool->round->sessions()->orderBy('start_at', 'asc')->first();
    }

    /**
     * Get the last session of the pool.
     *
     * @param Pool $pool
     *
     * @return Session|null
     */
    public function getPoolEndSession(Pool $pool): ?Session
    {
        return $pool->pool_round?->end_session ??
            $pool->round->sessions()->orderBy('start_at', 'desc')->first();
    }

    /**
     * Enable or disable a session for a pool.
     *
     * @param Pool $pool
     * @param int $sessionId    The session id
     *
     * @return void
     */
    public function enableSession(Pool $pool, int $sessionId)
    {
        // When the remitments are planned, don't enable or disable a session
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     return;
        // }
        $session = $this->tenantService->tontine()
            ->sessions()
            ->ofPool($pool)
            ->disabled($pool)
            ->find($sessionId);
        if(!$session)
        {
            return;
        }

        // Enable the session for the pool.
        $pool->disabled_sessions()->detach($session->id);
    }

    /**
     * Enable or disable a session for a pool.
     *
     * @param Pool $pool
     * @param int $sessionId    The session id
     *
     * @return void
     */
    public function disableSession(Pool $pool, int $sessionId)
    {
        // When the remitments are planned, don't enable or disable a session
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     return;
        // }
        $session = $this->tenantService->tontine()
            ->sessions()
            ->ofPool($pool)
            ->enabled($pool)
            ->find($sessionId);
        if(!$session)
        {
            return;
        }

        // Disable the session for the pool.
        DB::transaction(function() use($pool, $session) {
            $pool->disabled_sessions()->attach($session->id);

            $this->dataSyncService->syncPool($pool, true);
        });
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return bool
     */
    public function enabled(Pool $pool, Session $session): bool
    {
        return $session->disabled_pools->find($pool->id) === null;
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return bool
     */
    public function disabled(Pool $pool, Session $session): bool
    {
        return $session->disabled_pools->find($pool->id) !== null;
    }

    /**
     * Get the sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return Collection
     */
    public function getEnabledSessions(Pool $pool): Collection
    {
        return $this->tenantService->tontine()
            ->sessions()
            ->ofPool($pool)
            ->enabled($pool)
            ->orderBy('start_at')
            ->get();
    }

    /**
     * Get the number of sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return int
     */
    public function getEnabledSessionCount(Pool $pool): int
    {
        return $this->tenantService->tontine()
            ->sessions()
            ->ofPool($pool)
            ->enabled($pool)
            ->count();
    }
}
