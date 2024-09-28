<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;

use function trim;

/**
 * @databag subscription
 */
class MemberPage extends PageComponent
{
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

    public function html(): string
    {
        $search = trim($this->bag('subscription')->get('member.search', ''));
        $filter = $this->bag('subscription')->get('member.filter', null);
        $pool = $this->cl(Member::class)->getPool();

        return (string)$this->renderView('pages.planning.subscription.member.page', [
            'members' => $this->subscriptionService
                ->getMembers($pool, $search, $filter, $this->page),
            'total' => $this->subscriptionService->getSubscriptionCount($pool),
        ]);
    }

    protected function count(): int
    {
        $search = trim($this->bag('subscription')->get('member.search', ''));
        $filter = $this->bag('subscription')->get('member.filter', null);
        $pool = $this->cl(Member::class)->getPool();

        return $this->subscriptionService->getMemberCount($pool, $search, $filter);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('pool-subscription-members-page');

        return $this->response;
    }
}
