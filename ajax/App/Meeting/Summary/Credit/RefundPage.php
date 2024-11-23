<?php

namespace Ajax\App\Meeting\Summary\Credit;

use Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag refund
 */
class RefundPage extends PageComponent
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

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('summary.refund.fund');
        $filtered = $this->bag('refund')->get('filter', null);

        return $this->refundService->getDebtCount($session, $fund, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('summary.refund.fund');
        $filtered = $this->bag('refund')->get('filter', null);

        return $this->renderView('pages.meeting.summary.refund.page', [
            'session' => $session,
            'debts' => $this->refundService->getDebts($session, $fund, $filtered, $this->page),
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
