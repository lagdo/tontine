<?php

namespace App\Ajax\Web\Meeting\Summary\Credit;

use App\Ajax\Cache;
use App\Ajax\PageComponent;
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
    public function html(): string
    {
        $session = Cache::get('summary.session');
        $fund = Cache::get('summary.refund.partial.fund');

        return (string)$this->renderView('pages.meeting.summary.refund.partial.page', [
            'session' => $session,
            'refunds' => $this->refundService->getPartialRefunds($session, $fund, $this->page),
        ]);
    }

    protected function count(): int
    {
        $session = Cache::get('summary.session');
        $fund = Cache::get('summary.refund.partial.fund');

        return $this->refundService->getPartialRefundCount($session, $fund);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-partial-refunds-page');

        return $this->response;
    }
}
