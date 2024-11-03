<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;

use function trim;

/**
 * @databag subscription
 * @before getPool
 */
class Member extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = PoolSection::class;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(private PoolService $poolService,
        private SubscriptionService $subscriptionService)
    {}

    public function pool(int $poolId): AjaxResponse
    {
        $this->bag('subscription')->set('member.filter', null);
        $this->bag('subscription')->set('member.search', '');

        return $this->render();
    }

    public function html(): string
    {
        $pool = $this->cache->get('subscription.pool');
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
        $pool = $this->cache->get('subscription.pool');
        $this->subscriptionService->createSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        return $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId)
    {
        $pool = $this->cache->get('subscription.pool');
        $this->subscriptionService->deleteSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        return $this->cl(MemberPage::class)->page();
    }
}
