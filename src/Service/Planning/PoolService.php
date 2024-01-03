<?php

namespace Siak\Tontine\Service\Planning;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Service\TenantService;

use function trans;

class PoolService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
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
        return $this->getQuery()->find($poolId);
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

    /**
     * Get a paginated list of sessions.
     *
     * @param Pool $pool
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolSessions(Pool $pool, int $page = 0): Collection
    {
        return $pool->sessions()
            ->orderBy('start_at', 'asc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of sessions.
     *
     * @param Pool $pool
     *
     * @return int
     */
    public function getPoolSessionCount(Pool $pool): int
    {
        return $pool->sessions()->count();
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
        $session = $pool->sessions()->find($sessionId);
        if(!$session || $session->enabled($pool))
        {
            return;
        }

        // Enable the session for the pool.
        $pool->disabledSessions()->detach($session->id);
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
        if(!$session || $session->disabled($pool))
        {
            return;
        }

        // Disable the session for the pool.
        DB::transaction(function() use($pool, $session) {
            // If a session was already opened, delete the receivables and payables.
            // Will fail if any of them is already paid.
            $subscriptionIds = $pool->subscriptions()->pluck('id');
            $session->receivables()->whereIn('subscription_id', $subscriptionIds)->delete();
            $session->payables()->whereIn('subscription_id', $subscriptionIds)->delete();
            // Disable the session for the pool.
            $pool->disabledSessions()->attach($session->id);
        });
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
        return $pool->enabledSessions()->orderBy('start_at')->get();
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
        return $pool->enabledSessions()->count();
    }
}
