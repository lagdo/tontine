<?php

namespace App\Ajax\Web\Meeting\Session\Saving;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag meeting.saving
 * @before getFund
 */
class SavingPage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.saving', 'page'];

    /**
     * The constructor
     *
     * @param SavingService $savingService
     * @param FundService $fundService
     */
    public function __construct(protected SavingService $savingService,
        protected FundService $fundService)
    {}

    protected function getFund()
    {
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        Cache::set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.saving.fund');

        return (string)$this->renderView('pages.meeting.saving.page', [
            'session' => $session,
            'savings' => $this->savingService->getSavings($session, $fund, $this->page),
        ]);
    }

    protected function count(): int
    {
        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.saving.fund');

        return $this->savingService->getSavingCount($session, $fund);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-savings-page');

        return $this->response;
    }
}
