<?php

namespace Siak\Tontine\Service;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Subscription;

class SubscriptionService
{
    use Events\DebtEventTrait;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PlanningService
     */
    protected PlanningService $planningService;

    /**
     * @param TenantService $tenantService
     * @param PlanningService $planningService
     */
    public function __construct(TenantService $tenantService, PlanningService $planningService)
    {
        $this->tenantService = $tenantService;
        $this->planningService = $planningService;
    }

    /**
     * Get funds for the dropdown list.
     *
     * @return Collection
     */
    public function getFunds(): Collection
    {
        return $this->tenantService->round()->funds()->pluck('title', 'id');
    }

    /**
     * Get a single fund.
     *
     * @param int $fundId    The fund id
     *
     * @return Fund|null
     */
    public function getFund(int $fundId): ?Fund
    {
        return $this->tenantService->round()->funds()->find($fundId);
    }

    /**
     * Get the first fund.
     *
     * @return Fund|null
     */
    public function getFirstFund(): ?Fund
    {
        return $this->tenantService->round()->funds()->first();
    }

    /**
     * Get a paginated list of members.
     *
     * @param Fund $fund
     * @param bool $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Fund $fund, bool $filter, int $page = 0): Collection
    {
        $members = $this->tenantService->tontine()->members();
        if($filter)
        {
            // Return only members with subscription in this fund
            $members->whereHas('subscriptions', function(Builder $query) use($fund) {
                $query->where('subscriptions.fund_id', $fund->id);
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
            $member->subscriptionCount = $member->subscriptions()->where('fund_id', $fund->id)->count();
        }
        return $members;
    }

    /**
     * Get the number of members.
     *
     * @param Fund $fund
     * @param bool $filter
     *
     * @return int
     */
    public function getMemberCount(Fund $fund, bool $filter): int
    {
        $members = $this->tenantService->tontine()->members();
        if($filter)
        {
            // Return only members with subscription in this fund
            $members->whereHas('subscriptions', function(Builder $query) use($fund) {
                $query->where('subscriptions.fund_id', $fund->id);
            });
        }
        return $members->count();
    }

    /**
     * @param Fund $fund
     * @param int $memberId
     *
     * @return int
     */
    public function createSubscription(Fund $fund, int $memberId): int
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        $subscription = new Subscription();
        $subscription->title = '';
        $subscription->fund()->associate($fund);
        $subscription->member()->associate($member);

        DB::transaction(function() use($fund, $subscription) {
            // Create the subscription
            $subscription->save();
            $this->subscriptionCreated($fund, $subscription);
        });

        return $subscription->id;
    }

    /**
     * @param Fund $fund
     * @param int $memberId
     *
     * @return int
     */
    public function deleteSubscription(Fund $fund, int $memberId): int
    {
        $subscription = $fund->subscriptions()->where('member_id', $memberId)->first();
        if(!$subscription)
        {
            return 0;
        }

        DB::transaction(function() use($subscription) {
            $this->subscriptionDeleted($subscription);
            // Delete the subscription
            $subscription->delete();
        });

        return $subscription->id;
    }

    /**
     * Enable or disable a session for a fund.
     *
     * @param Fund $fund
     * @param Session $session
     *
     * @return bool
     */
    public function toggleSession(Fund $fund, Session $session): bool
    {
        DB::transaction(function() use($fund, $session) {
            if($session->enabled($fund))
            {
                $fund->disabledSessions()->attach($session->id);
                $this->fundDetached($fund, $session);
                return;
            }
            $fund->disabledSessions()->detach($session->id);
            $this->fundAttached($fund, $session);
        });
        return true;
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
     * Set or unset the beneficiary of a given fund.
     *
     * @param Fund $fund
     * @param Session $session
     * @param int $currSubscriptionId
     * @param int $nextSubscriptionId
     *
     * @return void
     */
    public function saveBeneficiary(Fund $fund, Session $session, int $currSubscriptionId, int $nextSubscriptionId)
    {
        DB::transaction(function() use($fund, $session, $currSubscriptionId, $nextSubscriptionId) {
            if($currSubscriptionId > 0)
            {
                $subscription = $fund->subscriptions()->find($currSubscriptionId);
                $this->unsetPayableSession($session, $subscription);
            }
            if($nextSubscriptionId > 0)
            {
                $subscription = $fund->subscriptions()->find($nextSubscriptionId);
                $this->setPayableSession($session, $subscription);
            }
        });
    }

    /**
     * Get the payables of a given fund.
     *
     * @param Fund $fund
     *
     * @return array
     */
    public function getPayables(Fund $fund): array
    {
        return $this->planningService->getPayables($fund);
    }

    /**
     * Get the receivables of a given fund.
     *
     * Will return basic data on subscriptions.
     *
     * @param Fund $fund
     *
     * @return array
     */
    public function getReceivables(Fund $fund): array
    {
        return $this->planningService->getReceivables($fund);
    }
}
