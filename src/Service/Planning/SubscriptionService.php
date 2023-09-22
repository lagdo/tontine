<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Subscription;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;

use function trans;

class SubscriptionService
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
     * Get pools for the dropdown list.
     *
     * @param bool $pluck
     *
     * @return Collection
     */
    public function getPools(bool $pluck = true): Collection
    {
        $query = $this->tenantService->round()->pools()
            ->with(['round.tontine'])->whereHas('subscriptions');
        return $pluck ? $query->get()->pluck('title', 'id') : $query->get();
    }

    /**
     * Get a paginated list of members.
     *
     * @param Pool $pool
     * @param bool $filter
     *
     * @return mixed
     */
    public function getQuery(Pool $pool, bool $filter)
    {
        $query = $this->tenantService->tontine()->members()->active();
        if($filter)
        {
            // Return only members with subscription in this pool
            $query = $query->whereHas('subscriptions', function(Builder $query) use($pool) {
                $query->where('subscriptions.pool_id', $pool->id);
            });
        }
        return $query;
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
        return $this->getQuery($pool, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->withCount([
                'subscriptions' => function(Builder $query) use($pool) {
                    $query->where('pool_id', $pool->id);
                },
            ])
            ->orderBy('name', 'asc')->get();
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
        return $this->getQuery($pool, $filter)->count();
    }

    /**
     * @param Pool $pool
     * @param int $memberId
     *
     * @return void
     */
    public function createSubscription(Pool $pool, int $memberId)
    {
        // Cannot modify subscriptions if a session is already opened.
        $this->sessionService->checkActiveSessions();

        // Enforce unique subscription per member in pool with variable deposit amount.
        if(!$pool->deposit_fixed &&
            $pool->subscriptions()->where('member_id', $memberId)->count() > 0)
        {
            return;
        }

        $member = $this->tenantService->tontine()->members()->find($memberId);
        $subscription = new Subscription();
        $subscription->title = '';
        $subscription->pool()->associate($pool);
        $subscription->member()->associate($member);

        DB::transaction(function() use($subscription) {
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
        // Cannot modify subscriptions if a session is already opened.
        $this->sessionService->checkActiveSessions();

        $subscription = $pool->subscriptions()->where('member_id', $memberId)->first();
        if(!$subscription)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }

        DB::transaction(function() use($subscription) {
            // Delete the payable
            $subscription->payable()->delete();
            // Delete the subscription
            $subscription->delete();
        });
    }

    /**
     * Get the number of subscriptions.
     *
     * @param Pool $pool
     *
     * @return int
     */
    public function getSubscriptionCount(Pool $pool): int
    {
        return $pool->subscriptions()->count();
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
     * @return bool
     */
    public function saveBeneficiary(Pool $pool, Session $session,
        int $currSubscriptionId, int $nextSubscriptionId): bool
    {
        $currSubscription = null;
        if($currSubscriptionId > 0)
        {
            $currSubscription = $pool->subscriptions()->find($currSubscriptionId);
            if(($currSubscription !== null && $currSubscription->payable !== null &&
                $currSubscription->payable->remitment !== null) || $session->closed)
            {
                // Can't chage the beneficiary if the session is closed or if
                // the collected amount has already been remitted.
                return false;
            }
        }
        $nextSubscription = $nextSubscriptionId > 0 ?
            $pool->subscriptions()->find($nextSubscriptionId) : null;

        DB::transaction(function() use($session, $currSubscription, $nextSubscription) {
            // If the beneficiary already has a session assigned, first remove it.
            if($currSubscription !== null)
            {
                $this->unsetPayableSession($currSubscription);
            }
            // If there is a new session assigned to the beneficiary, then save it.
            if($nextSubscription !== null)
            {
                $this->setPayableSession($nextSubscription, $session);
            }
        });

        return true;
    }
}
