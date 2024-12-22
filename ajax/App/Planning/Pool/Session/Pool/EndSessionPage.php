<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\PageComponent;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SessionService;
use Stringable;

/**
 * @databag pool.session
 * @before getPool
 */
class EndSessionPage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['pool.session', 'session.end.page'];

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
        $pool = $this->cache()->get('pool.session.pool');
        $endSession = $this->poolService->getPoolEndSession($pool);

        return $this->renderView('pages.planning.pool.session.end.page', [
            'sessions' => $this->sessionService->getTontineSessions($this->currentPage(), orderAsc: false),
            'sessionId' => !$endSession ? 0 : $endSession->id,
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
        $pool = $this->cache()->get('pool.session.pool');
        $session = $this->poolService->getPoolEndSession($pool);
        if(!$session)
        {
            return 1;
        }

        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);

        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    /**
     * Go to the page of the current end session
     */
    public function current(): AjaxResponse
    {
        return $this->page($this->getSessionPageNumber());
    }
}
