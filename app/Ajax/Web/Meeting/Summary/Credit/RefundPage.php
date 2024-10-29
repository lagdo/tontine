<?php

namespace App\Ajax\Web\Meeting\Summary\Credit;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\FundService;

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
    public function html(): string
    {
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('summary.refund.fund');
        $filtered = $this->bag('refund')->get('filter', null);

        return (string)$this->renderView('pages.meeting.summary.refund.page', [
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
