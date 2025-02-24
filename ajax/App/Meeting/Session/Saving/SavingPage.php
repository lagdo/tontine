<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\PageComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

/**
 * @databag meeting.saving
 * @before getFund
 */
class SavingPage extends PageComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.saving';

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
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

        return $this->savingService->getSavingCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

        return $this->renderView('pages.meeting.saving.page', [
            'session' => $session,
            'savings' => $this->savingService
                ->getSavings($session, $fund, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-savings-page');
    }
}
