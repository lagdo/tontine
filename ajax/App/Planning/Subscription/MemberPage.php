<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\PageComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Stringable;

use function trim;

/**
 * @databag subscription
 * @before getPool
 */
class MemberPage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['subscription', 'member.page'];

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param PoolService $poolService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(private LocaleService $localeService,
        private PoolService $poolService, private SubscriptionService $subscriptionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = trim($this->bag('subscription')->get('member.search', ''));
        $filter = $this->bag('subscription')->get('member.filter', null);
        $pool = $this->stash()->get('subscription.pool');

        return $this->subscriptionService->getMemberCount($pool, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = trim($this->bag('subscription')->get('member.search', ''));
        $filter = $this->bag('subscription')->get('member.filter', null);
        $pool = $this->stash()->get('subscription.pool');

        return $this->renderView('pages.planning.subscription.member.page', [
            'members' => $this->subscriptionService
                ->getMembers($pool, $search, $filter, $this->currentPage()),
            'total' => $this->subscriptionService->getSubscriptionCount($pool),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('pool-subscription-members-page');
    }
}
