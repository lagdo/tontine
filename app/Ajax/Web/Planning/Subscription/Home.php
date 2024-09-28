<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;

use function intval;
use function trans;

/**
 * @databag subscription
 * @before setPool
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
    protected function setPool()
    {
        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
    }

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

    public function pool(int $poolId = 0)
    {
        if($this->pool === null)
        {
            return $this->response;
        }

        $this->bag('subscription')->set('pool.id', $this->pool->id);
        $this->bag('subscription')->set('member.filter', null);
        $this->bag('subscription')->set('member.search', '');
        $this->bag('subscription')->set('session.filter', false);

        $html = $this->renderView('pages.planning.subscription.home');
        $this->response->html('content-home', $html);

        $this->cl(MemberPage::class)->page();
        $this->cl(SessionPage::class)->page();

        $this->response->js()->setSmScreenHandler('pool-subscription-sm-screens');

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

        $this->view()->shareValues($this->summaryService->getReceivables($this->pool));

        $html = $this->renderView('pages.planning.subscription.planning', [
            'pool' => $this->pool,
        ]);
        $this->response->html('content-page', $html);
        $this->response->js()->makeTableResponsive('content-page');

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
        $this->view()->shareValues($this->summaryService->getPayables($this->pool));

        $html = $this->renderView('pages.planning.subscription.beneficiaries', [
            'pool' => $this->pool,
            'pools' => $this->subscriptionService->getPools(),
        ]);
        $this->response->html('content-page', $html);
        $this->response->js()->makeTableResponsive('content-page');

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
            $this->notify->title(trans('common.titles.error'))->error($message);
        }

        return $this->beneficiaries();
    }
}
