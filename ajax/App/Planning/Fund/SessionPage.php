<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\PageComponent;
use Stringable;

/**
 * @databag planning.fund
 * @before getFund
 */
class SessionPage extends PageComponent
{
    use FundTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.fund', 'session.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->fundService->getGuildSessionCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.fund.session.page', [
            'fund' => $this->stash()->get('planning.fund'),
            'sessions' => $this->fundService
                ->getGuildSessions($this->currentPage(), orderAsc: false),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-planning-sessions-page');
    }

    private function getSessionPageNumber($session): int
    {
        $sessionCount = $this->fundService->getSessionCount($session, true, false);
        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    /**
     * Go to the page of the current start session
     */
    public function start()
    {
        $fund = $this->stash()->get('planning.fund');
        $this->page($this->getSessionPageNumber($fund->start));
    }

    /**
     * Go to the page of the current end session
     */
    public function end()
    {
        $fund = $this->stash()->get('planning.fund');
        $this->page($this->getSessionPageNumber($fund->end));
    }
}
