<?php

namespace Ajax\App\Planning\Pool;

use Ajax\PageComponent;
use Stringable;

/**
 * @databag planning.finance.pool
 * @before getPool
 */
class SessionPage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.finance.pool', 'session.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->poolService->getGuildSessionCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.page', [
            'pool' => $this->stash()->get('planning.finance.pool'),
            'sessions' => $this->poolService
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
        $sessionCount = $this->poolService->getSessionCount($session, true, false);
        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    /**
     * Go to the page of the current start session
     */
    public function start()
    {
        $pool = $this->stash()->get('planning.finance.pool');
        $this->page($this->getSessionPageNumber($pool->start));
    }

    /**
     * Go to the page of the current end session
     */
    public function end()
    {
        $pool = $this->stash()->get('planning.finance.pool');
        $this->page($this->getSessionPageNumber($pool->end));
    }
}
