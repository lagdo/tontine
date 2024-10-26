<?php

namespace App\Ajax\Web\Meeting\Session\Charge;

use App\Ajax\Cache;
use App\Ajax\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;

class LibreFeePage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'fee.libre.page'];

    /**
     * The constructor
     *
     * @param LibreFeeService $feeService
     */
    public function __construct(protected LibreFeeService $feeService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('meeting.session');

        return (string)$this->renderView('pages.meeting.charge.libre.page', [
            'session' => $session,
            'charges' => $this->feeService->getFees($this->page),
            'bills' => $this->feeService->getBills($session),
            'settlements' => $this->feeService->getSettlements($session),
        ]);
    }

    protected function count(): int
    {
        return $this->feeService->getFeeCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-fees-libre-page');

        return $this->response;
    }
}
