<?php

namespace Ajax\App\Planning\Pool\Round;

use Ajax\PageComponent;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag pool.round
 * @before getPool
 */
class EndSession extends PageComponent
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

        return $this->renderView('pages.planning.pool.round.sessions', [
            'field' => 'end',
            'rqPoolRoundSession' => $this->rq(),
            'sessions' => $this->sessionService->getTontineSessions($this->page, orderAsc: false),
            'sessionId' => $pool->pool_round ? $pool->pool_round->end_session_id : 0,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(EndSessionTitle::class)->render();

        $this->response->js()->makeTableResponsive('pool-round-sessions-end');
    }

    private function getSessionPageNumber(): int
    {
        $pool = $this->cache->get('planning.pool');
        if(!$pool->pool_round)
        {
            return 1;
        }

        $session = $pool->pool_round->start_session;
        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);
        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    public function showSessionPage(): AjaxResponse
    {
        $pool = $this->cache->get('planning.pool');
        if(!$pool)
        {
            return $this->response;
        }

        return $this->page($this->getSessionPageNumber());
    }
}