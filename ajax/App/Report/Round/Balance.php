<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\Meeting\Session\SummaryService;
use Siak\Tontine\Service\Report\RoundService;
use Stringable;

#[Before('checkHostAccess', ["report", "round"])]
#[Before('checkOpenedSessions')]
#[Before('getPools')]
class Balance extends Component
{
    use PoolTrait;

    /**
     * @param RoundService $roundService
     * @param SummaryService $summaryService
     */
    public function __construct(private RoundService $roundService,
        private SummaryService $summaryService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('tenant.session');
        $sessions = $this->roundService->getRoundSessions($round);
        if($session !== null)
        {
            $sessions = $sessions->filter(fn($_session) =>
                $_session->day_date <= $session->day_date);
        }
        $sessionIds = $sessions->pluck('id');

        return $this->renderView('pages.report.round.balance', [
            'sessions' => $sessions,
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'savings' => $this->roundService->getSavingAmounts($sessionIds),
            'transfers' => $this->roundService->getTransferAmounts($sessionIds),
            'outflows' => $this->roundService->getOutflowAmounts($sessionIds),
        ]);
    }

    /**
     * @param int $sessionId
     *
     * @return void
     */
    public function select(int $sessionId): void
    {
        $this->render();
    }
}
