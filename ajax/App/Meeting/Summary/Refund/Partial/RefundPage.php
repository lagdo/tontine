<?php

namespace Ajax\App\Meeting\Summary\Refund\Partial;

use Ajax\App\Meeting\Summary\PageComponent;
use Siak\Tontine\Service\Guild\FundService;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Stringable;

/**
 * @databag refund.partial
 */
class RefundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['refund.partial', 'principal.page'];

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param PartialRefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected PartialRefundService $refundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->stash()->get('summary.refund.partial.fund');

        return $this->refundService->getPartialRefundCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->stash()->get('summary.refund.partial.fund');

        return $this->renderView('pages.meeting.summary.refund.partial.page', [
            'session' => $session,
            'refunds' => $this->refundService->getPartialRefunds($session, $fund, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-summary-partial-refunds-page');
    }
}
