<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\PageComponent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
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
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

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
        $this->pool = $this->poolService->getPool($poolId);
    }

    public function html(): string
    {
        return (string)$this->renderView('pages.planning.pool.round.sessions', [
            'field' => 'end',
            'sessions' => $this->sessionService->getTontineSessions($this->page, orderAsc: false),
            'sessionId' => $this->pool->pool_round ? $this->pool->pool_round->end_session_id : 0,
        ]);
    }

    protected function count(): int
    {
        return $this->sessionService->getTontineSessionCount();
    }

    public function page(int $pageNumber = 0): ComponentResponse
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('pool-round-sessions-end');

        return $this->response;
    }

    private function getSessionPageNumber(SessionModel $session): int
    {
        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);

        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    /**
     * @exclude
     */
    public function pool(PoolModel $pool, int $pageNumber): ComponentResponse
    {
        $this->pool = $pool;

        return $this->page($pageNumber);
    }

    public function showSessionPage(): ComponentResponse
    {
        if(!$this->pool->pool_round)
        {
            return $this->response;
        }

        $pageNumber = $this->getSessionPageNumber($this->pool->pool_round->start_session);

        return $this->page($pageNumber);
    }
}
