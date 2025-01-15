<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Report\RoundService;
use Stringable;

class Balance extends Component
{
    /**
     * @param SessionService $sessionService
     * @param RoundService $roundService
     */
    public function __construct(private SessionService $sessionService,
        private RoundService $roundService)
    {}


    public function html(): Stringable
    {
        $sessions = $this->sessionService->getRoundSessions();
        $sessionIds = $sessions->filter(fn($session) =>
            ($session->opened || $session->closed))->pluck('id');

        return $this->renderView('pages.report.round.balance', [
            'sessions' => $sessions,
            'auctions' => $this->roundService->getAuctionAmounts($sessionIds),
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'savings' => $this->roundService->getSavingAmounts($sessionIds),
            'disbursements' => $this->roundService->getDisbursementAmounts($sessionIds),
        ]);
    }
}
