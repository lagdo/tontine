<?php

namespace App\Ajax\App\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Meeting\ReportService;

use function Jaxon\pm;
use function intval;

/**
 * @databag meeting
 * @before getPool
 */
class Report extends CallableClass
{
    /**
     * @di
     * @var ReportService
     */
    public ReportService $reportService;

    /**
     * @di
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
        $poolId = intval($this->target()->method() === 'select' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id', 0));
        if($poolId !== 0)
        {
            $this->pool = $this->subscriptionService->getPool($poolId);
        }
        if(!$this->pool)
        {
            $this->pool = $this->subscriptionService->getFirstPool();
            // Save the current pool id
            $this->bag('meeting')->set('pool.id', $this->pool ? $this->pool->id : 0);
        }
    }

    public function select(int $poolId, bool $showAmounts)
    {
        if(($this->pool))
        {
            $this->bag('meeting')->set('pool.id', $this->pool->id);
            return $showAmounts ? $this->amounts() : $this->deposits();
        }

        return $this->response;
    }

    public function home()
    {
        // Don't try to show the page if there is no pool selected.
        return $this->amounts();
    }

    public function amounts()
    {
        $this->view()->shareValues($this->reportService->getFigures($this->pool));
        $html = $this->view()->render('tontine.pages.meeting.report.amounts')
            ->with('pool', $this->pool)
            ->with('pools', $this->subscriptionService->getPools());
        $this->response->html('content-home', $html);

        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt(), true));
        $this->jq('#btn-meeting-report-refresh')->click($this->rq()->amounts());
        $this->jq('#btn-meeting-report-deposits')->click($this->rq()->deposits());
        $this->jq('#btn-meeting-report-print')->click($this->rq()->print());

        return $this->response;
    }

    public function deposits()
    {
        $this->view()->shareValues($this->reportService->getFigures($this->pool));
        $html = $this->view()->render('tontine.pages.meeting.report.deposits')
            ->with('pool', $this->pool)
            ->with('pools', $this->subscriptionService->getPools());
        $this->response->html('content-home', $html);

        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt(), false));
        $this->jq('#btn-meeting-report-refresh')->click($this->rq()->deposits());
        $this->jq('#btn-meeting-report-amounts')->click($this->rq()->amounts());
        $this->jq('#btn-meeting-report-print')->click($this->rq()->print());

        return $this->response;
    }

    public function print()
    {
        return $this->response;
    }
}
