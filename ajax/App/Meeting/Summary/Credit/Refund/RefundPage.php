<?php

namespace Ajax\App\Meeting\Summary\Credit\Refund;

use Ajax\App\Meeting\Summary\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Credit\RefundService;

#[Before('getFund')]
#[Databag('summary.refund')]
class RefundPage extends PageComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'summary.refund';

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary.refund', 'fund.page'];

    /**
     * The constructor
     *
     * @param RefundService $refundService
     */
    public function __construct(protected RefundService $refundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->getStashedFund();
        $filtered = $this->bag($this->bagId)->get('fund.filter', null);

        return $this->refundService->getDebtCount($session, $fund, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->getStashedFund();
        $filtered = $this->bag($this->bagId)->get('fund.filter', null);
        $debts = $this->refundService
            ->getDebts($session, $fund, $filtered, $this->currentPage());

        return $this->renderTpl('pages.meeting.summary.refund.page', [
            'debts' => $debts,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
