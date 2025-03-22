<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Siak\Tontine\Service\Report\RoundService;
use Stringable;

class Balance extends Component
{
    /**
     * @param RoundService $roundService
     */
    public function __construct(private RoundService $roundService)
    {}


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
            'disbursements' => $this->roundService->getDisbursementAmounts($sessionIds),
        ]);
    }
}
