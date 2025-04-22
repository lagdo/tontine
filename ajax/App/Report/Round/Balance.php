<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Report\RoundService;
use Stringable;

/**
 * @before getPools
 */
class Balance extends Component
{
    /**
     * @param RoundService $roundService
     * @param SummaryService $summaryService
     */
    public function __construct(private RoundService $roundService,
        private SummaryService $summaryService)
    {}

    protected function getPools(): void
    {
        $round = $this->tenantService->round();
        $figures = $this->summaryService->getFigures($round); 
        $pools = $this->summaryService->getPoolsBalance($figures);
        $this->view()->share('figures', $figures);
        $this->view()->share('pools', $pools);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->tenantService->round();
        $sessions = $this->roundService->getRoundSessions($round);
        $sessionIds = $sessions->pluck('id');

        return $this->renderView('pages.report.round.balance', [
            'sessions' => $sessions,
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'savings' => $this->roundService->getSavingAmounts($sessionIds),
            'outflows' => $this->roundService->getOutflowAmounts($sessionIds),
        ]);
    }
}
