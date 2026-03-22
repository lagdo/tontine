<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Report\RoundService;

use function collect;

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
        $session = $this->stash()->get('report.session');
        $sessionIds = collect([$session->id]);

        $sessionId = "{$session->id}";
        $this->stash()->set('report.total.deposits', $this->roundService
            ->getDepositAmounts($sessionIds)[$sessionId] ?? 0);
        $this->stash()->set('report.total.remitments', $this->roundService
            ->getRemitmentAmounts($sessionIds)[$sessionId] ?? 0);
        $this->stash()->set('report.total.settlements', $this->roundService
            ->getSettlementAmounts($sessionIds)[$sessionId] ?? 0);
        $this->stash()->set('report.total.loans', $this->roundService
            ->getLoanAmounts($sessionIds)[$sessionId] ?? 0);
        $this->stash()->set('report.total.refunds', $this->roundService
            ->getRefundAmounts($sessionIds)[$sessionId] ?? 0);
        $this->stash()->set('report.total.savings', $this->roundService
            ->getSavingAmounts($sessionIds)[$sessionId] ?? 0);
        $this->stash()->set('report.total.outflows', $this->roundService
            ->getOutflowAmounts($sessionIds)[$sessionId] ?? 0);

        // Initialize the page components.
        $this->cl(Graph\Summary::class)->render();
        $this->cl(Graph\Balance::class)->render();
        $this->cl(Graph\Inflow::class)->render();
        $this->cl(Graph\Outflow::class)->render();
    }
}
