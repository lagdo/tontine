<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\PageComponent;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SessionService;

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
    public function html(): string
    {
        $pool = $this->cache->get('pool.session.pool');

        return $this->renderView('pages.planning.pool.session.start.page', [
            'sessions' => $this->sessionService->getTontineSessions($this->page, orderAsc: false),
            'sessionId' => $pool->pool_round ? $pool->pool_round->start_session_id : 0,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(StartSessionTitle::class)->render();

        $this->response->js()->makeTableResponsive('pool-round-sessions-start');
    }

    private function getSessionPageNumber(): int
    {
        $pool = $this->cache->get('pool.session.pool');
        if(!$pool->pool_round)
        {
            return 1;
        }

        $session = $pool->pool_round->start_session;
        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);
        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    /**
     * Go to the page of the current start session
     */
    public function current(): AjaxResponse
    {
        return $this->page($this->getSessionPageNumber());
    }
}