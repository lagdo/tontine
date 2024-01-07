<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Subscription;
use Siak\Tontine\Service\TenantService;

use function strtolower;
use function trans;

class SubscriptionService
{
    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(protected TenantService $tenantService,
        protected PoolService $poolService)
    {}

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
     * @param string $search
     * @param bool $filter|null
     *
     * @return Builder|Relation
     */
    public function getQuery(Pool $pool, string $search, ?bool $filter): Builder|Relation
    {
        return $this->tenantService->tontine()->members()->active()
            ->when($filter === true, function(Builder $query) use($pool) {
                // Return only members with subscription in this pool
                return $query->whereHas('subscriptions', function(Builder $query) use($pool) {
                    $query->where('subscriptions.pool_id', $pool->id);
                });
            })
            ->when($filter === false, function(Builder $query) use($pool) {
                // Return only members without subscription in this pool
                return $query->whereDoesntHave('subscriptions', function(Builder $query) use($pool) {
                    $query->where('subscriptions.pool_id', $pool->id);
                });
            })
            ->when($search !== '', function($query) use($search) {
                $search = '%' . strtolower($search) . '%';
                return $query->where(DB::raw('lower(name)'), 'like', $search);
            });
    }

    /**
     * Get a paginated list of members.
     *
     * @param Pool $pool
     * @param string $search
     * @param bool $filter|null
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Pool $pool, string $search, ?bool $filter, int $page = 0): Collection
    {
        return $this->getQuery($pool, $search, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->withCount([
                'subscriptions' => function(Builder $query) use($pool) {
                    $query->where('pool_id', $pool->id);
                },
            ])
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the number of members.
     *
     * @param Pool $pool
     * @param string $search
     * @param bool $filter|null
     *
     * @return int
     */
    public function getMemberCount(Pool $pool, string $search, ?bool $filter): int
    {
        return $this->getQuery($pool, $search, $filter)->count();
    }

    /**
     * @param Pool $pool
     * @param int $memberId
     *
     * @return void
     */
    public function createSubscription(Pool $pool, int $memberId)
    {
        // When the remitments are planned, don't create a subscription
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     throw new MessageException(trans('tontine.subscription.errors.create'));
        // }

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
        // When the remitments are planned, don't delete a subscription
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     throw new MessageException(trans('tontine.subscription.errors.delete'));
        // }
        $subscriptions = $pool->subscriptions()
            ->where('member_id', $memberId)
            ->withCount('receivables')
            ->get()
            ->sortBy('receivables_count');
        if($subscriptions->count() === 0)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }

        // Since the subscriptions are sorted by receivables count, those with no receivable
        // will be deleted in priority.
        $subscription = $subscriptions->first();
        DB::transaction(function() use($subscription) {
            // Delete the receivables
            $subscription->receivables()->delete();
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
     * @param Session $session
     * @param Subscription|null $subscription
     *
     * @return bool
     */
    private function canChangeBeneficiary(Session $session, ?Subscription $subscription): bool
    {
        // Can't change the beneficiary if the session is closed,
        if($session->closed)
        {
            return false;
        }
        // Or if the collected amount has already been remitted.
        return $subscription === null || $subscription->payable === null ||
            $subscription->payable->remitment === null;
    }

    /**
     * Set or unset the beneficiary of a given pool.
     *
     * @param Pool $pool
     * @param int $sessionId
     * @param int $currSubscriptionId
     * @param int $nextSubscriptionId
     *
     * @return bool
     */
    public function saveBeneficiary(Pool $pool, int $sessionId, int $currSubscriptionId,
        int $nextSubscriptionId): bool
    {
        $session = $pool->sessions()->find($sessionId);
        $currSubscription = null;
        $nextSubscription = null;
        if($currSubscriptionId > 0)
        {
            $currSubscription = $pool->subscriptions()
                ->with('payable')
                ->find($currSubscriptionId);
            if(!$this->canChangeBeneficiary($session, $currSubscription))
            {
                return false;
            }
        }
        if($nextSubscriptionId > 0)
        {
            $nextSubscription = $pool->subscriptions()
                ->with('payable')
                ->find($nextSubscriptionId);
        }

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
