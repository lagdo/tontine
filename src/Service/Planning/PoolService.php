<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Service\DataSyncService;
use Siak\Tontine\Service\TenantService;

class PoolService
{
    use SessionTrait;

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
    public function getPoolDefs(Round $round, int $page = 0): Collection
    {
        return $this->tenantService->guild()->pools()
            ->with([
                'pools' => fn($query) => $query->ofRound($round),
            ])
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of pools.
     *
     * @return int
     */
    public function getPoolDefCount(): int
    {
        return $this->tenantService->guild()->pools()->count();
    }

    /**
     * @param int $defId
     *
     * @return void
     */
    public function enablePool(Round $round, int $defId): void
    {
        $def = $this->tenantService->guild()->pools()
            ->withCount([
                'pools' => fn($query) => $query->ofRound($round),
            ])
            ->find($defId);
        if(!$def || $def->pools_count > 0)
        {
            return;
        }

        // Create the pool
        $def->pools()->create([
            'round_id' => $round->id,
            'start_sid' => $round->start->id,
            'end_sid' => $round->end->id,
        ]);
    }

    /**
     * @param int $defId
     *
     * @return void
     */
    public function disablePool(Round $round, int $defId): void
    {
        $def = $this->tenantService->guild()->pools()
            ->withCount([
                'pools' => fn($query) => $query->ofRound($round),
            ])
            ->find($defId);
        if(!$def || $def->pools_count === 0)
        {
            return;
        }

        // Delete the pool
        $poolIds = $def->pools()->ofRound($round)->pluck('id');
        DB::table('pool_session_disabled')->whereIn('pool_id', $poolIds)->delete();
        Pool::whereIn('id', $poolIds)->delete();
    }

    /**
     * Get a paginated list of pools.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getPools(Round $round, int $page = 0): Collection
    {
        return Pool::ofRound($round)
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of pools.
     *
     * @return int
     */
    public function getPoolCount(Round $round): int
    {
        return Pool::ofRound($round)->count();
    }

    /**
     * Get a pool.
     *
     * @return Pool|null
     */
    public function getPool(Round $round, int $poolId): ?Pool
    {
        return Pool::ofRound($round)
            ->with(['sessions', 'disabled_sessions'])
            ->find($poolId);
    }

    /**
     * Get the sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return Collection
     */
    public function getActiveSessions(Pool $pool): Collection
    {
        return $pool->sessions()->orderBy('day_date')->get();
    }

    /**
     * Get the number of sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return int
     */
    public function getActiveSessionCount(Pool $pool): int
    {
        return $pool->sessions()->count();
    }

    /**
     * Save the pool sessions.
     *
     * @param Pool $pool
     * @param array $values
     *
     * @return void
     */
    public function saveSessions(Pool $pool, array $values)
    {
        DB::transaction(function() use($pool, $values) {
            $pool->update($values);

            // $this->dataSyncService->syncPool($pool, true);
        });
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
        $session = $pool->disabled_sessions()->find($sessionId);
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
        $session = $pool->sessions()->find($sessionId);
        if(!$session)
        {
            return;
        }

        // Disable the session for the pool.
        DB::transaction(function() use($pool, $session) {
            $pool->disabled_sessions()->attach($session->id);

            // $this->dataSyncService->syncPool($pool, true);
        });
    }
}
