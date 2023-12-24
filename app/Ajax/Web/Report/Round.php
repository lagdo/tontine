<?php

namespace App\Ajax\Web\Report;

use App\Ajax\CallableClass;
use App\Ajax\Web\Tontine\Options;
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
        $html = $this->render('pages.report.round.home')
            ->with('round', $this->tenantService->round());
        $this->response->html('content-home', $html);
        $this->jq('#btn-meeting-report-refresh')->click($this->rq()->home());
        $this->jq('#btn-tontine-options')->click($this->cl(Options::class)->rq()->editOptions());

        $this->pools();
        $this->amounts();

        return $this->response;
    }

    private function pools()
    {
        $html = '';
        $this->subscriptionService->getPools(false)
            ->each(function($pool) use(&$html) {
                $html .= $this->render('pages.report.round.pool',
                    $this->summaryService->getFigures($pool));
            });
        $this->response->html('content-pools', $html);
    }

    private function amounts()
    {
        $sessions = $this->tenantService->getSessions();
        // Sessions with data
        $sessionIds = $sessions->filter(function($session) {
            return $session->status === SessionModel::STATUS_CLOSED ||
                $session->status === SessionModel::STATUS_OPENED;
        })->pluck('id');
        $html = $this->render('pages.report.round.amounts', [
            'sessions' => $sessions,
            'auctions' => $this->roundService->getAuctionAmounts($sessionIds),
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'savings' => $this->roundService->getSavingAmounts($sessionIds),
            'disbursements' => $this->roundService->getDisbursementAmounts($sessionIds),
        ]);
        $this->response->html('content-amounts', $html);
    }
}
