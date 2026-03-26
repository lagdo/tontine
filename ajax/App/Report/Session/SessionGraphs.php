<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Report\RoundService;

#[Exclude]
class SessionGraphs extends Component
{
    /**
     * @var string
     */
    protected string $overrides = SessionTables::class;

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
        return $this->renderTpl('pages.report.session.graphs');
    }

    protected function after(): void
    {
        $sessions = $this->stash()->get('report.sessions');
        $sessionIds = $sessions->pluck('id');

        $this->stash()->set('report.total.deposits',
            $this->roundService->getDepositAmounts($sessionIds));
        $this->stash()->set('report.total.remitments',
            $this->roundService->getRemitmentAmounts($sessionIds));
        $this->stash()->set('report.total.settlements',
            $this->roundService->getSettlementAmounts($sessionIds));
        $this->stash()->set('report.total.loans',
            $this->roundService->getLoanAmounts($sessionIds));
        $this->stash()->set('report.total.refunds',
            $this->roundService->getRefundAmounts($sessionIds));
        $this->stash()->set('report.total.savings',
            $this->roundService->getSavingAmounts($sessionIds));
        $this->stash()->set('report.total.outflows',
            $this->roundService->getOutflowAmounts($sessionIds));

        $this->cl(Graph\Total::class)->render();
        $this->cl(Graph\Session::class)->render();
        $this->cl(Graph\Balance::class)->render();
        $this->cl(Graph\Inflow::class)->render();
        $this->cl(Graph\Outflow::class)->render();
    }
}
