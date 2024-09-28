<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use Siak\Tontine\Service\Planning\SubscriptionService;

use function trim;

/**
 * @databag subscription
 */
class Member extends Component
{
    /**
     * The constructor
     *
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(private SubscriptionService $subscriptionService)
    {}

    public function html(): string
    {
        $pool = $this->cl(Home::class)->getPool();
        return (string)$this->renderView('pages.planning.subscription.member.home', [
            'pool' => $pool,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
    }

    public function filter()
    {
        // Toggle the filter
        $filter = $this->bag('subscription')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('subscription')->set('member.filter', $filter);

        // Show the first page
        return $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('subscription')->set('member.search', trim($search));

        return $this->cl(MemberPage::class)->page();
    }

    public function create(int $memberId)
    {
        $pool = $this->cl(Home::class)->getPool();
        $this->subscriptionService->createSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        return $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId)
    {
        $pool = $this->cl(Home::class)->getPool();
        $this->subscriptionService->deleteSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        return $this->cl(MemberPage::class)->page();
    }
}
