<?php

namespace App\Ajax\Web\Meeting\Summary\Saving;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\Saving\SavingService;

/**
 * @databag refund
 */
class SavingPage extends PageComponent
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
     */
    public function __construct(protected SavingService $savingService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('meeting.saving.fund');

        return $this->savingService->getSavingCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('meeting.saving.fund');

        return (string)$this->renderView('pages.meeting.summary.saving.page', [
            'session' => $session,
            'savings' => $this->savingService->getSavings($session, $fund, $this->page),
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
