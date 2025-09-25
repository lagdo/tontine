<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\PoolDef;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Exception;

class PoolService
{
    use SessionTrait;

    /**
     * @param TenantService $tenantService
     * @param PoolSyncService $poolSyncService
     * @param FundSyncService $fundSyncService
     */
    public function __construct(protected TenantService $tenantService,
        private PoolSyncService $poolSyncService, private FundSyncService $fundSyncService)
    {}

    /**
     * @param Round $round
     * @param bool $filter|null
     *
     * @return Builder|Relation
     */
    public function getQuery(Round $round, ?bool $filter): Builder|Relation
    {
        $onRoundFilter = fn(Builder|Relation $q) => $q->ofRound($round);
        return $round->guild->pools()
            ->when($filter === true, fn(Builder|Relation $query) => $query
                ->whereHas('pools', $onRoundFilter))
            ->when($filter === false, fn(Builder|Relation $query) => $query
                ->whereDoesntHave('pools', $onRoundFilter));
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
                'pools' => fn(Builder|Relation $q) => $q->where('round_id', $round->id),
            ])
            ->withCount([
                'pools as pools_in_round_count' => fn(Builder|Relation $q) => $q->ofRound($round),
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
     * @return PoolDef|null
     */
    public function getPoolDef(Round $round, int $defId): ?PoolDef
    {
        return $round->guild->pools()
            ->with([
                'pools' => fn(Builder|Relation $q) => $q->where('round_id', $round->id),
            ])
            ->find($defId);
    }

    /**
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function enablePool(Round $round, int $defId): void
    {
        $def = $this->getPoolDef($round, $defId);
        if(!$def || $def->pools->count() > 0)
        {
            return; // Todo: throw an exception
        }

        $itemQuery = Pool::withoutGlobalScopes()->where('def_id', $defId);
        $startSession = $this->getStartSession($round, $itemQuery);
        $endSession = $this->getEndSession($round, $itemQuery);
        if($endSession->day_date <= $startSession->day_date)
        {
            return; // Todo: throw an exception
        }

        // Create the pool
        DB::transaction(function() use($def, $round, $startSession, $endSession) {
            $pool = $def->pools()->create([
                'title' => $def->title,
                'amount' => $def->amount,
                'deposit_fixed' => $def->deposit_fixed,
                'deposit_lendable' => $def->deposit_lendable,
                'remit_planned' => $def->remit_planned,
                'remit_auction' => $def->remit_auction,
                'round_id' => $round->id,
                'start_sid' => $startSession->id,
                'end_sid' => $endSession->id,
            ]);

            $this->fundSyncService->poolEnabled($round, $pool);
        });
    }

    /**
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function disablePool(Round $round, int $defId): void
    {
        $def = $this->getPoolDef($round, $defId);
        if(!$def || $def->pools->count() === 0)
        {
            return;
        }

        // Delete the pool
        DB::transaction(function() use($def, $round) {
            $pool = $def->pools->first();
            $this->fundSyncService->poolDisabled($round, $pool);

            DB::table('pool_session_disabled')
                ->where('pool_id', $pool->id)
                ->delete();
            $pool->delete();
        });
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

            $this->poolSyncService->sessionsChanged($pool);
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
        // Enable the session for the pool.
        DB::transaction(function() use($pool, $session) {
            DB::table('pool_session_disabled')
                ->where('pool_id', $pool->id)
                ->where('session_id', $session->id)
                ->delete();

            $this->poolSyncService->sessionEnabled($pool, $session);
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
        try
        {
            // Disable the session for the pool.
            DB::transaction(function() use($pool, $session) {
                DB::table('pool_session_disabled')
                    ->updateOrInsert([
                        'pool_id' => $pool->id,
                        'session_id' => $session->id,
                    ]);

                $this->poolSyncService->sessionDisabled($pool, $session);
            });
        }
        catch(Exception $e)
        {
            throw new MessageException(trans('tontine.pool.errors.cannot_remove'));
        }
    }

    /**
     * Get the number of active pools in the round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getPoolCount(Round $round): int
    {
        return $round->pools()->count();
    }
}
