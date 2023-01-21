<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Payable;
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
     * @return void
     */
    public function createSubscription(Pool $pool, int $memberId)
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
    }

    /**
     * @param Pool $pool
     * @param int $memberId
     *
     * @return void
     */
    public function deleteSubscription(Pool $pool, int $memberId)
    {
        $subscription = $pool->subscriptions()->where('member_id', $memberId)->first();
        if(!$subscription)
        {
            return;
        }

        DB::transaction(function() use($subscription) {
            // Delete the payable
            $subscription->payable()->delete();
            // Delete the subscription
            $subscription->delete();
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
    public function toggleSession(Pool $pool, Session $session)
    {
        if($session->disabled($pool))
        {
            // Enable the session for the pool.
            $pool->disabledSessions()->detach($session->id);
            return;
        }

        DB::transaction(function() use($pool, $session) {
            // Disable the session for the pool.
            $pool->disabledSessions()->attach($session->id);
            // Delete the beneficiaries for the pool on this session.
            Payable::where('session_id', $session->id)
                ->whereIn('subscription_id', $pool->subscriptions->pluck('id'))
                ->update(['session_id' => null]);
        });
    }

    /**
     * @param Subscription $subscription
     * @param Session $session
     *
     * @return void
     */
    public function setPayableSession(Subscription $subscription, Session $session)
    {
        $subscription->payable->session()->associate($session);
        $subscription->payable->save();
    }

    /**
     * @param Subscription $subscription
     *
     * @return void
     */
    public function unsetPayableSession(Subscription $subscription)
    {
        if(($subscription->payable->session_id))
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
                $this->unsetPayableSession($subscription);
            }
            // If there is a new session assigned to the beneficiary, then save it.
            if($nextSubscriptionId > 0)
            {
                $subscription = $pool->subscriptions()->find($nextSubscriptionId);
                $this->setPayableSession($subscription, $session);
            }
        });
    }
}
