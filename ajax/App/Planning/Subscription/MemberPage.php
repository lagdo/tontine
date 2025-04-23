<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\PageComponent;
use Ajax\App\Planning\Finance\Pool\PoolTrait;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Stringable;

use function trim;

/**
 * @databag planning.finance.pool
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
    protected array $bagOptions = ['planning.finance.pool', 'member.page'];

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
        $search = trim($this->bag('planning.finance.pool')->get('member.search', ''));
        $filter = $this->bag('planning.finance.pool')->get('member.filter', null);
        $pool = $this->stash()->get('planning.finance.pool');

        return $this->subscriptionService->getMemberCount($pool, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = trim($this->bag('planning.finance.pool')->get('member.search', ''));
        $filter = $this->bag('planning.finance.pool')->get('member.filter', null);
        $pool = $this->stash()->get('planning.finance.pool');

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
        $this->response->js('Tontine')->makeTableResponsive('content-subscription-members-page');
    }
}
