<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\PageComponent;
use Siak\Tontine\Model\Session as SessionModel;
use Stringable;

/**
 * @databag planning.pool
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
    protected array $bagOptions = ['planning.pool', 'session.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->poolService->getGuildSessionCount($guild);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->renderView('pages.planning.pool.session.page', [
            'pool' => $this->stash()->get('planning.pool'),
            'sessions' => $this->poolService
                ->getGuildSessions($guild, $this->currentPage(), orderAsc: false),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-planning-sessions-page');
    }

    /**
     * @param SessionModel $session
     *
     * @return int
     */
    private function getSessionPageNumber(SessionModel $session): int
    {
        $guild = $this->stash()->get('tenant.guild');
        $sessionCount = $this->poolService->getSessionCount($guild, $session, true, false);
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
