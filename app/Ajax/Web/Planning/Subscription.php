<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;
use Siak\Tontine\Service\LocaleService;

use function Jaxon\jq;
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
     * @var SubscriptionService
     */
    public SubscriptionService $subscriptionService;

    /**
     * @databag subscription
     * @before checkRoundPools
     * @after hideMenuOnMobile
     */
    public function home(int $poolId = 0)
    {
        $pools = $this->poolService->getRoundPools();
        $poolLabels = $pools->keyBy('id')->map(function($pool) {
            return $pool->title . ' - ' . ($pool->deposit_fixed ?
                $this->localeService->formatMoney($pool->amount) :
                trans('tontine.labels.types.libre'));
        });
        $html = $this->render('pages.planning.subscription.home', [
            'pools' => $poolLabels,
            'poolId' => $poolId,
        ]);
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);

        $selectPoolId = pm()->select('select-pool')->toInt();
        $this->jq('#btn-pool-select')->click($this->rq()->pool($selectPoolId));

        $pool = $pools->firstWhere('id', $poolId) ?? ($pools->count() > 0 ? $pools[0] : null);
        if(($pool))
        {
            return $this->show($pool);
        }

        return $this->response;
    }

    private function show(PoolModel $pool)
    {
        $this->response->html('subscriptions-pool-name',
            trans('tontine.pool.titles.subscriptions') . ' - ' . $pool->title);
        $this->response->html('content-page', $this->render('pages.planning.subscription.pool'));
        $this->cl(Subscription\Member::class)->show($pool);
        $this->cl(Subscription\Session::class)->show($pool);

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
        $html = $this->render('pages.planning.subscription.planning')
            ->with('pool', $pool);
        $this->response->html('content-page', $html);

        $this->jq('#btn-subscription-beneficiaries')->click($this->rq()->beneficiaries($poolId));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->planning($poolId));
        $this->jq('#btn-subscription-back')->click($this->rq()->pool($poolId));

        return $this->response;
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function beneficiaries(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        if(!$pool || !$pool->remit_planned)
        {
            return $this->response;
        }

        $this->response->html('section-title', trans('tontine.menus.planning'));
        $payables = $this->summaryService->getPayables($pool);
        $this->view()->shareValues($payables);
        $html = $this->render('pages.planning.subscription.beneficiaries')
            ->with('pool', $pool)
            ->with('pools', $this->subscriptionService->getPools());
        $this->response->html('content-page', $html);

        $this->jq('#btn-subscription-planning')->click($this->rq()->planning($poolId));
        $this->jq('#btn-pool-select')->click($this->rq()->select(pm()->select('select-pool')->toInt()));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->beneficiaries($poolId));
        $this->jq('#btn-subscription-back')->click($this->rq()->pool($poolId));
        $this->jq('.select-beneficiary')->change($this->rq()->saveBeneficiary($poolId,
            jq()->attr('data-session-id')->toInt(), jq()->val()->toInt(),
            jq()->attr('data-subscription-id')->toInt()));

        return $this->response;
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function saveBeneficiary(int $poolId, int $sessionId, int $nextSubscriptionId,
        int $currSubscriptionId)
    {
        $pool = $this->poolService->getPool($poolId);
        if(!$pool || !$pool->remit_planned || $pool->remit_auction)
        {
            return $this->response;
        }

        if(!$this->subscriptionService->saveBeneficiary($pool, $sessionId,
            $currSubscriptionId, $nextSubscriptionId))
        {
            $message = trans('tontine.beneficiary.errors.cant_change');
            $this->response->dialog->error($message, trans('common.titles.error'));
        }

        return $this->beneficiaries($poolId);
    }
}
