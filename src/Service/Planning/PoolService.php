<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Deposit;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;

use function trans;

class PoolService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     */
    public function __construct(TenantService $tenantService, SessionService $sessionService)
    {
        $this->tenantService = $tenantService;
        $this->sessionService = $sessionService;
    }

    /**
     * Get a paginated list of pools in the selected round.
     *
     * @param int $page
     *
     * @return array
     */
    public function getPools(int $page = 0)
    {
        $pools = $this->tenantService->round()->pools();
        if($page > 0 )
        {
            $pools->take($this->tenantService->getLimit());
            $pools->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $pools->get();
    }

    /**
     * Get the number of pools in the selected round.
     *
     * @return int
     */
    public function getPoolCount(): int
    {
        return $this->tenantService->round()->pools()->count();
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
    public function createPools(array $values): bool
    {
        // Cannot modify pools if a session is already opened.
        $this->sessionService->checkActiveSessions();

        DB::transaction(function() use($values) {
            $this->tenantService->round()->pools()->createMany($values);
        });

        return true;
    }

    /**
     * Update a pool.
     *
     * @param Pool $pool
     * @param array $values
     *
     * @return int
     */
    public function updatePool(Pool $pool, array $values): int
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
        // Cannot modify pools if a session is already opened.
        $this->sessionService->checkActiveSessions();

        if($pool->subscriptions()->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.subscription'));
        }
        // Delete the pool
        $pool->delete();
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
     * Get the number of sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return int
     */
    public function enabledSessionCount(Pool $pool): int
    {
        return $this->tenantService->round()->sessions->count() - $pool->disabledSessions->count();
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getLibrePoolAmount(Pool $pool, Session $session): int
    {
        // Sum the amounts for all deposits
        $receivableClosure = function($query) use($pool, $session) {
            $query->where('session_id', $session->id)
                ->whereHas('subscription', function($query) use($pool) {
                    $query->where('pool_id', $pool->id);
                });
        };

        return Deposit::whereHas('receivable', $receivableClosure)->sum('amount');
    }
}
