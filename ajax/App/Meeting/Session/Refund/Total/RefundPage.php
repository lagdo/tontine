<?php

namespace Ajax\App\Meeting\Session\Refund\Total;

use Ajax\App\Meeting\PageComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Stringable;

/**
 * @databag meeting.refund.final
 * @before getFund
 */
class RefundPage extends PageComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund.final';

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.refund.final', 'page'];

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
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $filtered = $this->bag($this->bagId)->get('filter', null);

        return $this->refundService->getDebtCount($session, $fund, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $filtered = $this->bag($this->bagId)->get('filter', null);
        $debts = $this->refundService
            ->getDebts($session, $fund, $filtered, $this->currentPage());

        return $this->renderView('pages.meeting.refund.final.page', [
            'session' => $session,
            'debts' => $debts,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
