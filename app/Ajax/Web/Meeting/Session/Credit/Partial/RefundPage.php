<?php

namespace App\Ajax\Web\Meeting\Session\Credit\Partial;

use App\Ajax\Web\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;

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
        $this->cache->set('meeting.refund.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.refund.fund');

        return $this->refundService->getPartialRefundCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.refund.fund');

        return (string)$this->renderView('pages.meeting.refund.partial.page', [
            'session' => $session,
            'refunds' => $this->refundService->getPartialRefunds($session, $fund, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-partial-refunds-page');
    }
}
