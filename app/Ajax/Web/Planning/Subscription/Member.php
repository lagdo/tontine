<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\CallableClass;
use App\Ajax\Web\Planning\Subscription;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;

use function intval;
use function Jaxon\jq;
use function Jaxon\pm;
use function trim;

/**
 * @databag subscription
 * @before getPool
 */
class Member extends CallableClass
{
    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(private PoolService $poolService,
        private SubscriptionService $subscriptionService)
    {
        $this->poolService = $poolService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'home' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
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
        $html = $this->render('pages.planning.subscription.member.home')
            ->with('pool', $this->pool);
        $this->response->html('pool-subscription-members', $html);
        $this->jq('#btn-subscription-members-filter')->click($this->rq()->filter());
        $this->jq('#btn-subscription-members-refresh')->click($this->rq()->home($poolId));
        if($this->pool->remit_planned)
        {
            $this->jq('#btn-subscription-beneficiaries')
                ->click($this->cl(Subscription::class)->rq()->beneficiaries($poolId));
        }

        $this->bag('subscription')->set('pool.id', $poolId);
        $this->bag('subscription')->set('member.filter', null);

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $search = trim($this->bag('subscription')->get('member.search', ''));
        $filter = $this->bag('subscription')->get('member.filter', null);
        $memberCount = $this->subscriptionService->getMemberCount($this->pool,
            $search, $filter);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount,
            'subscription', 'member.page');
        $members = $this->subscriptionService->getMembers($this->pool, $search,
            $filter, $pageNumber);
        $pagination = $this->rq()->page(pm()->page())->paginate($pageNumber,
            $perPage, $memberCount);

        $html = $this->render('pages.planning.subscription.member.page', [
            'search' => $search,
            'members' => $members,
            'pagination' => $pagination,
            'total' => $this->subscriptionService->getSubscriptionCount($this->pool),
        ]);
        $this->response->html('pool-subscription-members-page', $html);

        $memberId = jq()->parent()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-subscription-member-add')->click($this->rq()->create($memberId));
        $this->jq('.btn-subscription-member-del')->click($this->rq()->delete($memberId));
        $this->jq('#btn-subscription-members-search')
            ->click($this->rq()->search(jq('#txt-subscription-members-search')->val()));

        return $this->response;
    }

    public function filter()
    {
        // Toggle the filter
        $filter = $this->bag('subscription')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('subscription')->set('member.filter', $filter);

        // Show the first page
        return $this->page(1);
    }

    public function search(string $search)
    {
        $this->bag('subscription')->set('member.search', trim($search));

        return $this->page();
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
