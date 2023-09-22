<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SummaryService;
use Siak\Tontine\Service\LocaleService;

use function Jaxon\pm;
use function trans;

class Subscription extends CallableClass
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
     * @after hideMenuOnMobile
     */
    public function home(int $poolId = 0)
    {
        $pools = $this->poolService->getPools();
        $poolLabels = $pools->keyBy('id')->map(function($pool) {
            return $pool->title . ' - ' . ($pool->deposit_fixed ?
                $this->localeService->formatMoney($pool->amount) :
                trans('tontine.labels.types.libre'));
        });
        $html = $this->view()->render('tontine.pages.planning.subscription.home')
            ->with('pools', $poolLabels)
            ->with('poolId', $poolId);
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);

        $selectPoolId = pm()->select('select-pool')->toInt();
        $this->jq('#btn-pool-select')->click($this->rq()->pool($selectPoolId));

        $pool = $pools->firstWhere('id', $poolId) ?? ($pools->count() > 0 ? $pools[0] : null);
        if(($pool))
        {
            // Show the pool subscriptions
            return $this->show($pool);
        }

        return $this->response;
    }

    private function show(PoolModel $pool)
    {
        $this->cl(Subscription\Member::class)->show($pool);
        $this->cl(Subscription\Session::class)->show($pool);
        if($pool->remit_planned)
        {
            $this->jq('#btn-subscription-planning')->click($this->rq()->planning($pool->id));
        }

        return $this->response;
    }

    public function pool(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);

        return !$pool ? $this->response : $this->show($pool);
    }

    /**
     * @di $summaryService
     */
    public function planning(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        if(!$pool || !$pool->remit_planned)
        {
            return $this->response;
        }

        $receivables = $this->summaryService->getReceivables($pool);
        $this->view()->shareValues($receivables);
        $html = $this->view()->render('tontine.pages.planning.subscription.planning')
            ->with('pool', $pool);
        $this->response->html('content-home', $html);

        $this->jq('#btn-subscription-refresh')->click($this->rq()->planning($poolId));
        $this->jq('#btn-subscription-back')->click($this->rq()->home($poolId));

        return $this->response;
    }
}
