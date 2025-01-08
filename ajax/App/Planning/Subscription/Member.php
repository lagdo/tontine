<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Stringable;

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

    public function pool(int $poolId)
    {
        $this->bag('subscription')->set('member.filter', null);
        $this->bag('subscription')->set('member.search', '');

        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.subscription.member.home', [
            'pool' => $this->stash()->get('subscription.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();

        $this->response->js('Tontine')
            ->showSmScreen('content-subscription-members', 'subscription-sm-screens');
    }

    public function filter()
    {
        // Toggle the filter
        $filter = $this->bag('subscription')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('subscription')->set('member.filter', $filter);

        // Show the first page
        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('subscription')->set('member.search', trim($search));

        $this->cl(MemberPage::class)->page();
    }

    public function create(int $memberId)
    {
        $pool = $this->stash()->get('subscription.pool');
        $this->subscriptionService->createSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId)
    {
        $pool = $this->stash()->get('subscription.pool');
        $this->subscriptionService->deleteSubscription($pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        $this->cl(MemberPage::class)->page();
    }
}
