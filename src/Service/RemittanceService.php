<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Session;

use Illuminate\Support\Collection;

class RemittanceService
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
        return $this->tenantService->round()->pools()->with(['subscriptions.payable.remittance'])->find($poolId);
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return mixed
     */
    private function getQuery(Pool $pool, Session $session)
    {
        return $session->payables()->whereIn('subscription_id', $pool->subscriptions()->pluck('id'));
    }

    /**
     * @param Pool $pool
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPayables(Pool $pool, Session $session, int $page = 0): Collection
    {
        $payables = $this->getQuery($pool, $session)->with(['subscription.member', 'remittance']);
        if($page > 0 )
        {
            $payables->take($this->tenantService->getLimit());
            $payables->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $payables->get();
    }

    /**
     * Get the number of payables in the selected round.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getPayableCount(Pool $pool, Session $session): int
    {
        return $this->getQuery($pool, $session)->count();
    }

    /**
     * Find the unique payable for a pool and a session.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     *
     * @return Payable|null
     */
    public function getPayable(Pool $pool, Session $session, int $payableId): ?Payable
    {
        return $this->getQuery($pool, $session)->where('id', $payableId)->first();
    }

    /**
     * Create a remittance.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     * @param int $amountPaid
     *
     * @return void
     */
    public function createRemittance(Pool $pool, Session $session, int $payableId, int $amountPaid = 0): void
    {
        $payable = $this->getPayable($pool, $session, $payableId);
        if(!$payable || $payable->remittance)
        {
            return;
        }
        $payable->remittance()->create(['paid_at' => now(), 'amount_paid' => $amountPaid]);
    }

    /**
     * Delete a remittance.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     *
     * @return void
     */
    public function deleteRemittance(Pool $pool, Session $session, int $payableId): void
    {
        $payable = $this->getPayable($pool, $session, $payableId);
        if(!$payable || !$payable->remittance)
        {
            return;
        }
        $payable->remittance()->delete();
    }
}
