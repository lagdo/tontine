<?php

namespace App\Ajax\App\Planning;

use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Tontine\TenantService;
use App\Ajax\CallableClass;

use function intval;
use function Jaxon\jq;
use function Jaxon\pm;

/**
 * @databag subscription
 * @before getFund
 */
class Subscription extends CallableClass
{
    /**
     * @var TenantService
     */
    public TenantService $tenantService;

    /**
     * @di
     * @var SubscriptionService
     */
    public SubscriptionService $subscriptionService;

    /**
     * @var FundModel|null
     */
    protected ?FundModel $fund;

    /**
     * @return void
     */
    protected function getFund()
    {
        $fundId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : intval($this->bag('subscription')->get('fund.id'));
        $this->fund = $this->subscriptionService->getFund($fundId);
    }

    public function home(int $fundId)
    {
        $html = $this->view()->render('pages.planning.subscription.home')
            ->with('fund', $this->fund);
        $this->response->html('subscription-home', $html);
        $this->jq('#btn-subscription-filter')->click($this->rq()->filter());
        $this->jq('#btn-subscription-refresh')->click($this->rq()->home($fundId));
        $this->jq('#btn-subscription-deposits')->click($this->rq()->deposits());
        $this->jq('#btn-subscription-remittances')->click($this->rq()->remittances());

        $this->bag('subscription')->set('fund.id', $fundId);
        $this->bag('subscription')->set('filter', false);
        return $this->page($this->bag('subscription')->get('page', 1));
    }

    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('subscription')->get('page', 1);
        }
        $this->bag('subscription')->set('page', $pageNumber);
        $filter = $this->bag('subscription')->get('filter', false);

        $members = $this->subscriptionService->getMembers($this->fund, $filter, $pageNumber);
        $memberCount = $this->subscriptionService->getMemberCount($this->fund, $filter);

        $html = $this->view()->render('pages.planning.subscription.page')
            ->with('members', $members)
            ->with('pagination', $this->rq()->page(pm()->page(), $filter)->paginate($pageNumber, 10, $memberCount));
        $this->response->html('subscription-page', $html);

        $memberId = jq()->parent()->parent()->attr('data-member-id')->toInt();
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
        $this->subscriptionService->createSubscription($this->fund, $memberId);
        $this->page(); // Refresh the current page
        // $this->notify->success(trans('tontine.subscription.messages.created'), trans('common.titles.success'));

        return $this->response;
    }

    public function delete(int $memberId)
    {
        $this->subscriptionService->deleteSubscription($this->fund, $memberId);
        $this->page(); // Refresh the current page
        // $this->notify->success(trans('tontine.subscription.messages.deleted'), trans('common.titles.success'));

        return $this->response;
    }
}
