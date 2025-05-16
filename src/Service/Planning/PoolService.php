<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
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
     * @param Round $round
     * @param bool $filter|null
     *
     * @return Builder|Relation
     */
    public function getQuery(Round $round, ?bool $filter): Builder|Relation
    {
        return $round->guild->pools()
            ->when($filter === true, fn(Builder $query) => $query
                ->whereHas('pools', fn($q) => $q->ofRound($round)))
            ->when($filter === false, fn(Builder $query) => $query
                ->whereDoesntHave('pools', fn($q) => $q->ofRound($round)));
    }

    /**
     * Get a paginated list of pools.
     *
     * @param Round $round
     * @param bool $filter|null
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolDefs(Round $round, ?bool $filter, int $page = 0): Collection
    {
        return $this->getQuery($round, $filter)
            ->with([
                'pools' => fn($query) => $query->ofRound($round),
            ])
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of pools.
     *
     * @param Round $round
     * @param bool $filter|null
     *
     * @return int
     */
    public function getPoolDefCount(Round $round, ?bool $filter): int
    {
        return $this->getQuery($round, $filter)->count();
    }

    /**
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function enablePool(Round $round, int $defId): void
    {
        $def = $round->guild->pools()
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
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function disablePool(Round $round, int $defId): void
    {
        $def = $round->guild->pools()
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
     * @param Round $round
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
     * @param Round $round
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
     * @param Round $round
     * @param int $poolId
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
     * @param Session $session
     *
     * @return void
     */
    public function enableSession(Pool $pool, Session $session)
    {
        // When the remitments are planned, don't enable or disable a session
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     return;
        // }

        // Enable the session for the pool.
        DB::transaction(function() use($pool, $session) {
            DB::table('pool_session_disabled')
                ->where('pool_id', $pool->id)
                ->where('session_id', $session->id)
                ->delete();

            // $this->dataSyncService->syncPool($pool, true);
        });
    }

    /**
     * Enable or disable a session for a pool.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    public function disableSession(Pool $pool, Session $session)
    {
        // When the remitments are planned, don't enable or disable a session
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     return;
        // }

        // Disable the session for the pool.
        DB::transaction(function() use($pool, $session) {
            DB::table('pool_session_disabled')
                ->updateOrInsert([
                    'pool_id' => $pool->id,
                    'session_id' => $session->id,
                ]);

            // $this->dataSyncService->syncPool($pool, true);
        });
    }
}
