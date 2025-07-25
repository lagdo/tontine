<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\PageComponent;
use Ajax\App\Planning\Pool\PoolTrait;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Stringable;

/**
 * @databag planning.pool
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
    protected array $bagOptions = ['planning.pool', 'member.page'];

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param PoolService $poolService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(private LocaleService $localeService,
        PoolService $poolService, private SubscriptionService $subscriptionService)
    {
        $this->poolService = $poolService;
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('planning.pool')->get('member.search', '');
        $filter = $this->bag('planning.pool')->get('member.filter', null);
        $pool = $this->stash()->get('planning.pool');

        return $this->subscriptionService->getMemberCount($pool, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = $this->bag('planning.pool')->get('member.search', '');
        $filter = $this->bag('planning.pool')->get('member.filter', null);
        $pool = $this->stash()->get('planning.pool');

        return $this->renderView('pages.planning.pool.subscription.member.page', [
            'members' => $this->subscriptionService
                ->getMembers($pool, $search, $filter, $this->currentPage()),
            'total' => $this->subscriptionService->getSubscriptionCount($pool),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-subscription-members-page');
    }
}
