<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

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
        $this->cache->set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.saving.fund');

        return $this->savingService->getSavingCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.saving.fund');

        return $this->renderView('pages.meeting.saving.page', [
            'session' => $session,
            'savings' => $this->savingService->getSavings($session, $fund, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-savings-page');
    }
}
