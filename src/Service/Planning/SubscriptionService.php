<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Subscription;
use Siak\Tontine\Service\Tontine\TenantService;

class SubscriptionService
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
     * Get pools for the dropdown list.
     *
     * @return Collection
     */
    public function getPools(): Collection
    {
        return $this->tenantService->round()->pools()->pluck('title', 'id');
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
     * Get the first pool.
     *
     * @return Pool|null
     */
    public function getFirstPool(): ?Pool
    {
        return $this->tenantService->round()->pools()->first();
    }

    /**
     * Get a paginated list of members.
     *
     * @param Pool $pool
     * @param bool $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Pool $pool, bool $filter, int $page = 0): Collection
    {
        $members = $this->tenantService->tontine()->members();
        if($filter)
        {
            // Return only members with subscription in this pool
            $members->whereHas('subscriptions', function(Builder $query) use($pool) {
                $query->where('subscriptions.pool_id', $pool->id);
            });
        }
        if($page > 0 )
        {
            $members->take($this->tenantService->getLimit());
            $members->skip($this->tenantService->getLimit() * ($page - 1));
        }
        $members = $members->get();
        foreach($members as &$member)
        {
            $member->subscriptionCount = $member->subscriptions()->where('pool_id', $pool->id)->count();
        }
        return $members;
    }

    /**
     * Get the number of members.
     *
     * @param Pool $pool
     * @param bool $filter
     *
     * @return int
     */
    public function getMemberCount(Pool $pool, bool $filter): int
    {
        $members = $this->tenantService->tontine()->members();
        if($filter)
        {
            // Return only members with subscription in this pool
            $members->whereHas('subscriptions', function(Builder $query) use($pool) {
                $query->where('subscriptions.pool_id', $pool->id);
            });
        }
        return $members->count();
    }

    /**
     * @param Pool $pool
     * @param int $memberId
     *
     * @return int
     */
    public function createSubscription(Pool $pool, int $memberId): int
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        $subscription = new Subscription();
        $subscription->title = '';
        $subscription->pool()->associate($pool);
        $subscription->member()->associate($member);

        DB::transaction(function() use($pool, $subscription) {
            // Create the subscription
            $subscription->save();
            // Create the payable
            $subscription->payable()->create([]);
        });

        return $subscription->id;
    }

    /**
     * @param Pool $pool
     * @param int $memberId
     *
     * @return int
     */
    public function deleteSubscription(Pool $pool, int $memberId): int
    {
        $subscription = $pool->subscriptions()->where('member_id', $memberId)->first();
        if(!$subscription)
        {
            return 0;
        }

        DB::transaction(function() use($subscription) {
            // Delete the payable
            $subscription->payable()->delete();
            // Delete the subscription
            $subscription->delete();
        });

        return $subscription->id;
    }

    /**
     * Enable or disable a session for a pool.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    public function toggleSession(Pool $pool, Session $session)
    {
        DB::transaction(function() use($pool, $session) {
            if($session->enabled($pool))
            {
                // Add the session to the list of disabled sessiosn for the pool.
                $pool->disabledSessions()->attach($session->id);
                return;
            }
            // Remove the session from the list of disabled sessiosn for the pool.
            $pool->disabledSessions()->detach($session->id);
        });
    }

    /**
     * @param Session $session
     * @param Subscription $subscription
     *
     * @return void
     */
    public function setPayableSession(Session $session, Subscription $subscription)
    {
        $subscription->payable->session()->associate($session);
        $subscription->payable->save();
    }

    /**
     * @param Session $session
     * @param Subscription $subscription
     *
     * @return void
     */
    public function unsetPayableSession(Session $session, Subscription $subscription)
    {
        if($subscription->payable->session_id === $session->id)
        {
            $subscription->payable->session()->dissociate();
            $subscription->payable->save();
        }
    }

    /**
     * Set or unset the beneficiary of a given pool.
     *
     * @param Pool $pool
     * @param Session $session
     * @param int $currSubscriptionId
     * @param int $nextSubscriptionId
     *
     * @return void
     */
    public function saveBeneficiary(Pool $pool, Session $session, int $currSubscriptionId, int $nextSubscriptionId)
    {
        DB::transaction(function() use($pool, $session, $currSubscriptionId, $nextSubscriptionId) {
            // If the beneficiary already has a session assigned, first remove it.
            if($currSubscriptionId > 0)
            {
                $subscription = $pool->subscriptions()->find($currSubscriptionId);
                $this->unsetPayableSession($session, $subscription);
            }
            // If there is a new session assigned to the beneficiary, then save it.
            if($nextSubscriptionId > 0)
            {
                $subscription = $pool->subscriptions()->find($nextSubscriptionId);
                $this->setPayableSession($session, $subscription);
            }
        });
    }
}
