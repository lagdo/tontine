<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;
use Siak\Tontine\Service\TenantService;

use function intval;
use function Jaxon\jq;
use function Jaxon\pm;

/**
 * @databag report
 * @before getPool
 */
class Beneficiary extends CallableClass
{
    /**
     * @di
     * @var TenantService
     */
    public TenantService $tenantService;

    /**
     * @di
     * @var SubscriptionService
     */
    public SubscriptionService $subscriptionService;

    /**
     * @di
     * @var SummaryService
     */
    public SummaryService $summaryService;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'select' ?
            $this->target()->args()[0] : $this->bag('report')->get('pool.id', 0);
        if($poolId !== 0)
        {
            $this->pool = $this->subscriptionService->getPool(intval($poolId));
        }
        if(!$this->pool)
        {
            $this->pool = $this->subscriptionService->getFirstPool();
            // Save the current pool id
            $this->bag('report')->set('pool.id', $this->pool ? $this->pool->id : 0);
        }
    }

    public function select(int $poolId)
    {
        if(($this->pool))
        {
            $this->bag('report')->set('pool.id', $this->pool->id);
        }

        return $this->home();
    }

    /**
     * @after hideMenuOnMobile
     */
    public function home()
    {
        if(!$this->pool)
        {
            return $this->response;
        }

        $this->response->html('section-title', trans('tontine.menus.planning'));
        $payables = $this->summaryService->getPayables($this->pool);
        $this->view()->shareValues($payables);
        $html = $this->view()->render('tontine.pages.planning.beneficiary.page')
            ->with('tontine', $this->tenantService->tontine())
            ->with('pool', $this->pool)
            ->with('pools', $this->subscriptionService->getPools());
        $this->response->html('content-home', $html);

        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt()));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->home());
        $this->jq('.select-beneficiary')->change($this->rq()->save(jq()->attr('data-session-id')->toInt(),
            jq()->attr('data-subscription-id')->toInt(), jq()->val()->toInt()));

        return $this->response;
    }

    public function save(int $sessionId, int $currSubscriptionId, int $nextSubscriptionId)
    {
        $session = $this->tenantService->getSession($sessionId);
        if(!$this->subscriptionService->saveBeneficiary($this->pool, $session,
            $currSubscriptionId, $nextSubscriptionId))
        {
            $message = trans('tontine.beneficiary.errors.cant_change');
            $this->response->dialog->error($message, trans('common.titles.error'));
        }

        // Refresh the page, even in case of error, since the displayed beneficiary has changed.
        return $this->home();
    }
}
