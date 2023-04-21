<?php

namespace App\Ajax\App\Balance;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\ReportService;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\TenantService;

use function intval;
use function Jaxon\jq;
use function Jaxon\pm;

/**
 * @databag report
 * @before getPool
 */
class Planning extends CallableClass
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
     * @var SessionService
     */
    public SessionService $sessionService;

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

        return $this->amounts();
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

        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt()));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->amounts());
        $this->jq('.pool-session-toggle')->click($this->rq()->toggleSession(jq()->attr('data-session-id')->toInt()));

        return $this->response;
    }

    /**
     * @di $tenantService
     * @di $sessionService
     */
    public function toggleSession(int $sessionId)
    {
        $session = $this->tenantService->getSession($sessionId);
        $this->sessionService->toggleSession($this->pool, $session);

        return $this->amounts();
    }
}
