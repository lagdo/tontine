<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\FuncComponent;
use Ajax\App\Planning\Pool\PoolTrait;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;

use function trim;

/**
 * @databag planning.finance.pool
 * @before getPool
 */
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

    public function filter()
    {
        // Toggle the filter
        $filter = $this->bag('planning.finance.pool')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.finance.pool')->set('member.filter', $filter);

        // Show the first page
        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('planning.finance.pool')->set('member.search', trim($search));

        $this->cl(MemberPage::class)->page();
    }

    public function create(int $memberId)
    {
        $pool = $this->stash()->get('planning.finance.pool');
        $this->subscriptionService->createSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId)
    {
        $pool = $this->stash()->get('planning.finance.pool');
        $this->subscriptionService->deleteSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        $this->cl(MemberPage::class)->page();
    }
}
