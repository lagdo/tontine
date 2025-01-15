<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SessionService;
use Stringable;

/**
 * @databag pool.session
 * @before getPool
 */
class StartSessionPage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['pool.session', 'session.start.page'];

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param SessionService $sessionService
     */
    public function __construct(private PoolService $poolService,
        private SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->sessionService->getTontineSessionCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('pool.session.pool');
        $startSession = $this->poolService->getPoolStartSession($pool);

        return $this->renderView('pages.planning.pool.session.start.page', [
            'sessions' => $this->sessionService->getTontineSessions($this->currentPage(), orderAsc: false),
            'sessionId' => !$startSession ? 0 : $startSession->id,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(StartSessionTitle::class)->render();

        $this->response->js('Tontine')->makeTableResponsive('content-planning-start-sessions-page');
    }

    private function getSessionPageNumber(): int
    {
        $pool = $this->stash()->get('pool.session.pool');
        $session = $this->poolService->getPoolStartSession($pool);
        if(!$session)
        {
            return 1;
        }

        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);

        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    /**
     * Go to the page of the current start session
     */
    public function current()
    {
        $this->page($this->getSessionPageNumber());
    }
}
