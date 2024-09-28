<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;

use function intval;
use function trans;
use function trim;

/**
 * @databag subscription
 * @before getPool
 */
class Member extends Component
{
    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

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
     * @exclude
     *
     * @return PoolModel|null
     */
    public function getPool(): ?PoolModel
    {
        if(!$this->pool)
        {
            $poolId = intval($this->bag('subscription')->get('pool.id'));
            $this->pool = $this->poolService->getPool($poolId);
        }

        return $this->pool;
    }

    /**
     * @exclude
     */
    public function show(PoolModel $pool)
    {
        $this->pool = $pool;
        $this->refresh();
    }

    public function refresh()
    {
        $this->bag('subscription')->set('member.filter', null);
        $this->bag('subscription')->set('member.search', '');

        $this->render();
        $this->cl(MemberPage::class)->page();

        return $this->response;
    }

    public function html(): string
    {
        $poolLabels = $this->poolService
            ->getRoundPools()
            ->keyBy('id')->map(function($pool) {
                return $pool->title . ' - ' . ($pool->deposit_fixed ?
                    $this->localeService->formatMoney($pool->amount) :
                    trans('tontine.labels.types.libre'));
            });
        return (string)$this->renderView('pages.planning.subscription.member.home', [
            'pool' => $this->pool,
            'pools' => $poolLabels,
        ]);
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
        $this->subscriptionService->createSubscription($this->pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        return $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId)
    {
        $this->subscriptionService->deleteSubscription($this->pool, $memberId);

        // Refresh the current page
        $this->cl(MemberCounter::class)->render();
        return $this->cl(MemberPage::class)->page();
    }
}
