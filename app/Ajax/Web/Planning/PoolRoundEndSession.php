<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\PageComponent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag pool.round
 * @before getPool
 */
class PoolRoundEndSession extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['pool.round', 'session.end.page'];

    /**
     * The constructor
     *
     * @param SessionService $sessionService
     * @param PoolService $poolService
     */
    public function __construct(private SessionService $sessionService,
        private PoolService $poolService)
    {}

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = (int)$this->bag('pool.round')->get('pool.id');
        $this->cache->set('planning.pool', $this->poolService->getPool($poolId));
    }

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
        $pool = $this->cache->get('planning.pool');

        return (string)$this->renderView('pages.planning.pool.round.sessions', [
            'field' => 'end',
            'sessions' => $this->sessionService->getTontineSessions($this->page, orderAsc: false),
            'sessionId' => $pool->pool_round ? $pool->pool_round->end_session_id : 0,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('pool-round-sessions-end');
    }

    public function showSessionPage(): ComponentResponse
    {
        $pool = $this->cache->get('planning.pool');
        if(!$pool || !$pool->pool_round)
        {
            return $this->response;
        }

        $session = $pool->pool_round->start_session;
        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);
        $pageNumber = (int)($sessionCount / $this->tenantService->getLimit()) + 1;

        return $this->page($pageNumber);
    }
}
