<?php

namespace Ajax\App\Meeting\Session\Credit;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag refund
 */
class RefundPage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['refund', 'principal.page'];

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param RefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected RefundService $refundService)
    {}

    protected function getFund()
    {
        $fundId = $this->bag('refund')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->cache()->set('meeting.refund.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->cache()->get('meeting.session');
        $fund = $this->cache()->get('meeting.refund.fund');
        $filtered = $this->bag('refund')->get('filter', null);

        return $this->refundService->getDebtCount($session, $fund, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache()->get('meeting.session');
        $fund = $this->cache()->get('meeting.refund.fund');
        $filtered = $this->bag('refund')->get('filter', null);

        return $this->renderView('pages.meeting.refund.page', [
            'session' => $session,
            'debts' => $this->refundService->getDebts($session, $fund, $filtered, $this->pageNumber()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-debts-page');
    }
}
