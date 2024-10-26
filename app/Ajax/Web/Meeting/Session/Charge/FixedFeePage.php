<?php

namespace App\Ajax\Web\Meeting\Session\Charge;

use App\Ajax\Cache;
use App\Ajax\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;

class FixedFeePage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'fee.fixed.page'];

    /**
     * The constructor
     *
     * @param FixedFeeService $feeService
     */
    public function __construct(protected FixedFeeService $feeService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('meeting.session');

        return (string)$this->renderView('pages.meeting.charge.fixed.page', [
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

        $this->response->js()->makeTableResponsive('meeting-fees-fixed-page');

        return $this->response;
    }
}
