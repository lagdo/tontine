<?php

namespace Ajax\App\Meeting\Summary\Credit;

use Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag refund.partial
 */
class PartialRefundPage extends PageComponent
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
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('summary.refund.partial.fund');

        return $this->refundService->getPartialRefundCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('summary.refund.partial.fund');

        return $this->renderView('pages.meeting.summary.refund.partial.page', [
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
