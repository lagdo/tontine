<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Report\RoundService;

#[Exclude]
class RoundGraphs extends Component
{
    /**
     * @var string
     */
    protected string $overrides = RoundTables::class;

    /**
     * @param RoundService $roundService
     */
    public function __construct(private RoundService $roundService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.report.round.graphs');
    }

    protected function after(): void
    {
        $lastSession = $this->stash()->get('report.session');
        $sessions = $this->stash()->get('report.sessions')
            ->filter(fn($session) => $session->day_date <= $lastSession->day_date);
        $sessionIds = $sessions->pluck('id');

        $this->stash()->set('report.total.deposits', $this->roundService
            ->getDepositAmounts($sessionIds)->sum());
        $this->stash()->set('report.total.remitments', $this->roundService
            ->getRemitmentAmounts($sessionIds)->sum());
        $this->stash()->set('report.total.settlements', $this->roundService
            ->getSettlementAmounts($sessionIds)->sum());
        $this->stash()->set('report.total.loans', $this->roundService
            ->getLoanAmounts($sessionIds)->sum());
        $this->stash()->set('report.total.refunds', $this->roundService
            ->getRefundAmounts($sessionIds)->sum());
        $this->stash()->set('report.total.savings', $this->roundService
            ->getSavingAmounts($sessionIds)->sum());
        $this->stash()->set('report.total.outflows', $this->roundService
            ->getOutflowAmounts($sessionIds)->sum());

        // Initialize the page components.
        $this->cl(Graph\Summary::class)->render();
        $this->cl(Graph\Balance::class)->render();
        $this->cl(Graph\Inflow::class)->render();
        $this->cl(Graph\Outflow::class)->render();
    }
}
