<?php

namespace App\Ajax\Web\Meeting\Session\Credit;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag refund.partial
 * @before getFund
 */
class PartialRefundPage extends MeetingPageComponent
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
        Cache::set('meeting.refund.partial.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.refund.partial.fund');

        return (string)$this->renderView('pages.meeting.refund.partial.page', [
            'session' => $session,
            'refunds' => $this->refundService->getPartialRefunds($session, $fund, $this->page),
        ]);
    }

    protected function count(): int
    {
        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.refund.partial.fund');

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
