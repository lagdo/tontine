<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Model\Session as SessionModel;

#[Before('getPool')]
#[Databag('planning.pool')]
class SessionPage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.pool', 'session.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->poolService->getGuildSessionCount($this->guild());
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.pool.session.page', [
            'pool' => $this->stash()->get('planning.pool'),
            'sessions' => $this->poolService
                ->getGuildSessions($this->guild(), $this->currentPage(), orderAsc: false),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-planning-sessions-page');
    }

    /**
     * @param SessionModel $session
     *
     * @return int
     */
    private function getSessionPageNumber(SessionModel $session): int
    {
        $sessionCount = $this->poolService->getSessionCount($this->guild(), $session, true, false);
        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    /**
     * Go to the page of the current start session
     */
    public function start()
    {
        $pool = $this->stash()->get('planning.pool');
        $this->page($this->getSessionPageNumber($pool->start));
    }

    /**
     * Go to the page of the current end session
     */
    public function end()
    {
        $pool = $this->stash()->get('planning.pool');
        $this->page($this->getSessionPageNumber($pool->end));
    }
}
