<?php

namespace App\Ajax\Web\Meeting\Summary\Credit;

use App\Ajax\Cache;
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
    public function html(): string
    {
        $session = Cache::get('summary.session');
        $fund = Cache::get('summary.refund.fund');
        $filtered = $this->bag('refund')->get('filter', null);

        return (string)$this->renderView('pages.meeting.summary.refund.page', [
            'session' => $session,
            'debts' => $this->refundService->getDebts($session, $fund, $filtered, $this->page),
        ]);
    }

    protected function count(): int
    {
        $session = Cache::get('summary.session');
        $fund = Cache::get('summary.refund.fund');
        $filtered = $this->bag('refund')->get('filter', null);

        return $this->refundService->getDebtCount($session, $fund, $filtered);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-debts-page');

        return $this->response;
    }
}
