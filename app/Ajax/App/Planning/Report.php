<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\ReportService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Tontine\TenantService;

use function intval;
use function Jaxon\jq;
use function Jaxon\pm;

/**
 * @databag report
 * @before getPool
 */
class Report extends CallableClass
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
     * @di
     * @var ReportService
     */
    public ReportService $reportService;

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

    public function select(int $poolId, bool $showDeposits)
    {
        if(($this->pool))
        {
            $this->bag('report')->set('pool.id', $this->pool->id);
        }

        return $showDeposits ? $this->amounts() : $this->remitments();
    }

    public function home()
    {
        // Don't try to show the page if there is no pool selected.
        return ($this->pool) ? $this->amounts() : $this->response;
    }

    public function amounts()
    {
        $receivables = $this->reportService->getReceivables($this->pool);
        $this->view()->shareValues($receivables);
        $html = $this->view()->render('tontine.pages.planning.report.amounts')
            ->with('pool', $this->pool)
            ->with('pools', $this->subscriptionService->getPools());
        $this->response->html('content-home', $html);

        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt(), true));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->amounts());
        $this->jq('#btn-subscription-deposits')->click($this->rq()->deposits());
        $this->jq('#btn-subscription-remitments')->click($this->rq()->remitments());
        $this->jq('.pool-session-toggle')->click($this->rq()->toggleSession(jq()->attr('data-session-id')->toInt()));

        return $this->response;
    }

    public function deposits()
    {
        $receivables = $this->reportService->getReceivables($this->pool);
        $this->view()->shareValues($receivables);
        $html = $this->view()->render('tontine.pages.planning.report.deposits')
            ->with('pool', $this->pool)
            ->with('pools', $this->subscriptionService->getPools());
        $this->response->html('content-home', $html);

        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt(), true));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->deposits());
        $this->jq('#btn-subscription-amounts')->click($this->rq()->amounts());
        $this->jq('#btn-subscription-remitments')->click($this->rq()->remitments());
        $this->jq('.pool-session-toggle')->click($this->rq()->toggleSession(jq()->attr('data-session-id')->toInt()));

        return $this->response;
    }

    /**
     * @di $tenantService
     */
    public function toggleSession(int $sessionId)
    {
        $session = $this->tenantService->getSession(intval($sessionId));
        $this->subscriptionService->toggleSession($this->pool, $session);

        return $this->amounts();
    }

    public function remitments()
    {
        $payables = $this->reportService->getPayables($this->pool);
        $this->view()->shareValues($payables);
        $html = $this->view()->render('tontine.pages.planning.report.remitments')
            ->with('pool', $this->pool)
            ->with('pools', $this->subscriptionService->getPools());
        $this->response->html('content-home', $html);

        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt(), false));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->remitments());
        $this->jq('#btn-subscription-amounts')->click($this->rq()->amounts());
        $this->jq('#btn-subscription-deposits')->click($this->rq()->deposits());
        $this->jq('.select-beneficiary')->change($this->rq()->saveBeneficiary(jq()->attr('data-session-id')->toInt(),
            jq()->attr('data-subscription-id')->toInt(), jq()->val()->toInt()));

        return $this->response;
    }

    /**
     * @di $tenantService
     */
    public function saveBeneficiary(int $sessionId, int $currSubscriptionId, int $nextSubscriptionId)
    {
        $session = $this->tenantService->getSession($sessionId);
        $this->subscriptionService->saveBeneficiary($this->pool, $session, $currSubscriptionId, $nextSubscriptionId);

        return $this->remitments();
    }
}
