<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function trans;

class DepositService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
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
        return $this->tenantService->round()->pools()->find($poolId);
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return mixed
     */
    private function getQuery(Pool $pool, Session $session)
    {
        return $session->receivables()->whereIn('subscription_id', $pool->subscriptions()->pluck('id'));
    }

    /**
     * @param Pool $pool
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getReceivables(Pool $pool, Session $session, int $page = 0): Collection
    {
        $receivables = $this->getQuery($pool, $session)->with(['subscription.member', 'deposit']);
        if($page > 0 )
        {
            $receivables->take($this->tenantService->getLimit());
            $receivables->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $receivables->orderBy('id', 'asc')->get();
    }

    /**
     * Get the number of receivables in the selected round.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getReceivableCount(Pool $pool, Session $session): int
    {
        return $this->getQuery($pool, $session)->count();
    }

    /**
     * Find the unique receivable for a pool and a session.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return Receivable|null
     */
    public function getReceivable(Pool $pool, Session $session, int $receivableId): ?Receivable
    {
        return $this->getQuery($pool, $session)->where('id', $receivableId)->first();
    }

    /**
     * Create a deposit.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return void
     */
    public function createDeposit(Pool $pool, Session $session, int $receivableId): void
    {
        $receivable = $this->getReceivable($pool, $session, $receivableId);
        if(!$receivable || $receivable->deposit)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        $receivable->deposit()->create(['paid_at' => now()]);
    }

    /**
     * Delete a deposit.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return void
     */
    public function deleteDeposit(Pool $pool, Session $session, int $receivableId): void
    {
        $receivable = $this->getReceivable($pool, $session, $receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if(($receivable->deposit->online))
        {
            throw new MessageException(trans('tontine.subscription.errors.online'));
        }
        $receivable->deposit()->delete();
    }
}
