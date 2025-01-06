<?php

namespace Ajax\App\Meeting\Session\Credit\Partial;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag refund.partial
 * @before getFund
 */
class RefundPage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['partial.refund', 'principal.page'];

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param PartialRefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected PartialRefundService $refundService)
    {}

    protected function getFund()
    {
        $fundId = $this->bag('partial.refund')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->stash()->set('meeting.refund.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.refund.fund');

        return $this->refundService->getPartialRefundCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.refund.fund');

        return $this->renderView('pages.meeting.refund.partial.page', [
            'session' => $session,
            'refunds' => $this->refundService->getPartialRefunds($session, $fund, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-partial-refunds-page');
    }
}
