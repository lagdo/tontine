<?php

namespace Ajax\App\Meeting\Summary\Credit\Refund;

use Ajax\App\Meeting\Summary\PageComponent;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Stringable;

/**
 * @databag summary.refund
 * @before getFund
 */
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
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->getStashedFund();
        $filtered = $this->bag($this->bagId)->get('fund.filter', null);
        $debts = $this->refundService
            ->getDebts($session, $fund, $filtered, $this->currentPage());

        return $this->renderView('pages.meeting.summary.refund.page', [
            'debts' => $debts,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
