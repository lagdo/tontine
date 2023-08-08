<?php

namespace App\Ajax\App\Report;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Report\RoundService;
use Siak\Tontine\Service\TenantService;

/**
 * @databag meeting
 */
class Round extends CallableClass
{
    /**
     * @di
     * @var SummaryService
     */
    protected SummaryService $summaryService;

    /**
     * @di
     * @var SubscriptionService
     */
    protected SubscriptionService $subscriptionService;

    /**
     * @di
     * @var RoundService
     */
    protected RoundService $roundService;

    /**
     * @di
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $html = $this->view()->render('tontine.pages.report.round.home');
        $this->response->html('content-home', $html);
        $this->jq('#btn-meeting-report-refresh')->click($this->rq()->home());

        $this->pools();
        $this->amounts();

        return $this->response;
    }

    private function pools()
    {
        $html = '';
        $this->subscriptionService->getPools(false)
            ->each(function($pool) use(&$html) {
                $html .= $this->view()->render('tontine.pages.report.round.pool',
                    $this->summaryService->getFigures($pool));
            });
        $this->response->html('content-pools', $html);
    }

    private function amounts()
    {
        $sessions = $this->tenantService->round()->sessions;
        // Sessions with data
        $sessionIds = $sessions->filter(function($session) {
            return $session->status === SessionModel::STATUS_CLOSED ||
                $session->status === SessionModel::STATUS_OPENED;
        })->pluck('id');
        $html = $this->view()->render('tontine.pages.report.round.amounts', [
            'sessions' => $sessions,
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'fundings' => $this->roundService->getFundingAmounts($sessionIds),
            'disbursements' => $this->roundService->getDisbursementAmounts($sessionIds),
        ]);
        $this->response->html('content-amounts', $html);
    }
}
