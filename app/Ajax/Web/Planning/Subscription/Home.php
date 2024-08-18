<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag subscription
 * @before getPool
 */
class Home extends CallableClass
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @di
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var SummaryService
     */
    public SummaryService $summaryService;

    /**
     * @var SubscriptionService
     */
    public SubscriptionService $subscriptionService;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * @return void
     */
    protected function getPool()
    {
        if($this->target()->method() === 'home')
        {
            return;
        }

        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
    }

    /**
     * @before checkGuestAccess ["planning", "subscriptions"]
     * @before checkRoundPools
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $html = $this->renderView('pages.planning.subscription.home');
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);

        $poolId = intval($this->bag('subscription')->get('pool.id', 0));
        $pools = $this->poolService->getRoundPools();
        $pool = $pools->firstWhere('id', $poolId) ?? ($pools->count() > 0 ? $pools[0] : null);
        if($pool === null)
        {
            return $this->response;
        }

        $this->pool = $pool;
        $this->response->call('setSmScreenHandler', 'pool-subscription-sm-screens');

        return $this->pool();
    }

    public function pool(int $poolId = 0)
    {
        if($this->pool === null)
        {
            return $this->response;
        }

        $this->bag('subscription')->set('pool.id', $this->pool->id);

        $this->cl(Member::class)->show($this->pool);
        $this->cl(Session::class)->show($this->pool);

        if($poolId > 0)
        {
            $message = trans('tontine.pool.messages.selected', [
                'tontine' => $this->pool->title,
            ]);
            $this->response->dialog->info($message, trans('common.titles.info'));
        }

        return $this->response;
    }

    /**
     * @di $summaryService
     */
    public function planning()
    {
        if(!$this->pool || !$this->pool->remit_planned)
        {
            return $this->response;
        }

        $receivables = $this->summaryService->getReceivables($this->pool);
        $this->view()->shareValues($receivables);
        $html = $this->renderView('pages.planning.subscription.planning', [
            'pool' => $this->pool,
        ]);
        $this->response->html('content-page', $html);
        $this->response->call('makeTableResponsive', 'content-page');

        $this->jq('#btn-subscription-beneficiaries')->click($this->rq()->beneficiaries());
        $this->jq('#btn-subscription-refresh')->click($this->rq()->planning());
        $this->jq('#btn-subscription-back')->click($this->rq()->home());

        return $this->response;
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function beneficiaries()
    {
        if(!$this->pool || !$this->pool->remit_planned)
        {
            return $this->response;
        }

        $this->response->html('section-title', trans('tontine.menus.planning'));
        $payables = $this->summaryService->getPayables($this->pool);
        $this->view()->shareValues($payables);
        $html = $this->renderView('pages.planning.subscription.beneficiaries', [
            'pool' => $this->pool,
            'pools' => $this->subscriptionService->getPools(),
        ]);
        $this->response->html('content-page', $html);
        $this->response->call('makeTableResponsive', 'content-page');

        $this->jq('#btn-subscription-planning')->click($this->rq()->planning());
        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt()));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->beneficiaries());
        $this->jq('#btn-subscription-back')->click($this->rq()->home());
        $this->jq('.select-beneficiary')->change($this->rq()
            ->saveBeneficiary(jq()->attr('data-session-id')->toInt(), jq()->val()->toInt(),
                jq()->attr('data-subscription-id')->toInt()));

        return $this->response;
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function saveBeneficiary(int $sessionId, int $nextSubscriptionId, int $currSubscriptionId)
    {
        if(!$this->pool || !$this->pool->remit_planned || $this->pool->remit_auction)
        {
            return $this->response;
        }

        if(!$this->subscriptionService->saveBeneficiary($this->pool, $sessionId,
            $currSubscriptionId, $nextSubscriptionId))
        {
            $message = trans('tontine.beneficiary.errors.cant_change');
            $this->response->dialog->error($message, trans('common.titles.error'));
        }

        return $this->beneficiaries();
    }
}
