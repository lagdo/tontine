<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\TenantService;

use function intval;
use function Jaxon\jq;
use function Jaxon\pm;

/**
 * @databag subscription
 * @before getPool
 */
class Subscription extends CallableClass
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var SubscriptionService
     */
    protected SubscriptionService $subscriptionService;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool;

    /**
     * The constructor
     *
     * @param TenantService $tenantService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(TenantService $tenantService, SubscriptionService $subscriptionService)
    {
        $this->tenantService = $tenantService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->subscriptionService->getPool($poolId);
    }

    /**
     * @exclude
     */
    public function show(PoolModel $pool)
    {
        $this->pool = $pool;
        return $this->home($pool->id);
    }

    public function home(int $poolId)
    {
        $html = $this->view()->render('tontine.pages.planning.subscription.home')
            ->with('pool', $this->pool);
        $this->response->html('subscription-home', $html);
        $this->jq('#btn-subscription-filter')->click($this->rq()->filter());
        $this->jq('#btn-subscription-refresh')->click($this->rq()->home($poolId));

        $this->bag('subscription')->set('pool.id', $poolId);
        $this->bag('subscription')->set('filter', false);
        return $this->page($this->bag('subscription')->get('page', 1));
    }

    public function page(int $pageNumber = 0)
    {
        $filter = $this->bag('subscription')->get('filter', false);
        $memberCount = $this->subscriptionService->getMemberCount($this->pool, $filter);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount, 'subscription', 'page');
        $members = $this->subscriptionService->getMembers($this->pool, $filter, $pageNumber);
        $pagination = $this->rq()->page(pm()->page())->paginate($pageNumber, $perPage, $memberCount);

        $tontine = $this->tenantService->tontine();
        $html = $this->view()->render('tontine.pages.planning.subscription.page')
            ->with('tontine', $tontine)
            ->with('members', $members)
            ->with('count', $this->subscriptionService->getSubscriptionCount($this->pool))
            ->with('pagination', $pagination);
        $this->response->html('subscription-page', $html);

        $memberId = $tontine->is_libre ? jq()->parent()->attr('data-member-id')->toInt() :
            jq()->parent()->parent()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-subscription-add')->click($this->rq()->create($memberId));
        $this->jq('.btn-subscription-del')->click($this->rq()->delete($memberId));

        return $this->response;
    }

    public function filter()
    {
        // Toggle the filter
        $filter = $this->bag('subscription')->get('filter', false);
        $this->bag('subscription')->set('filter', !$filter);

        // Show the first page
        return $this->page(1);
    }

    public function create(int $memberId)
    {
        $this->subscriptionService->createSubscription($this->pool, $memberId);

        // Refresh the current page
        return $this->page();
    }

    public function delete(int $memberId)
    {
        $this->subscriptionService->deleteSubscription($this->pool, $memberId);

        // Refresh the current page
        return $this->page();
    }
}
