<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\FuncComponent;
use Ajax\App\Planning\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;

#[Before('getPool')]
#[Databag('planning.pool')]
class MemberFunc extends FuncComponent
{
    use PoolTrait;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(PoolService $poolService,
        private SubscriptionService $subscriptionService)
    {
        $this->poolService = $poolService;
    }

    public function create(int $memberId)
    {
        $pool = $this->stash()->get('planning.pool');
        $this->subscriptionService->createSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId)
    {
        $pool = $this->stash()->get('planning.pool');
        $this->subscriptionService->deleteSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        $this->cl(MemberPage::class)->page();
    }
}
