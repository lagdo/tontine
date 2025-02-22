<?php

namespace Ajax\App\Planning\Financial;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\SessionService;
use Stringable;

/**
 * @databag planning.financial
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
    protected array $bagOptions = ['planning.financial', 'session.page'];

    /**
     * The constructor
     *
     * @param SessionService $sessionService
     */
    public function __construct(private SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->sessionService->getTontineSessionCount();
    }

    private function isCandidate($session, $pool, $startSession, $endSession): void
    {
        $session->candidate = $session->start_at->lte($endSession->start_at) &&
            $session->start_at->gte($startSession->start_at);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('planning.financial.pool');
        $startSession = $this->poolService->getPoolStartSession($pool);
        $endSession = $this->poolService->getPoolEndSession($pool);
        \Log::debug('Pool page sessions', [
            'start' => $startSession->start_at,
            'pool_start' => $pool->start_at,
            'end' => $endSession->start_at,
            'pool_end' => $pool->end_at,
        ]);
        $sessions = $this->sessionService
            ->getTontineSessions($this->currentPage(), orderAsc: false)
            ->each(function($session) use($pool, $startSession, $endSession) {
                $this->isCandidate($session, $pool, $startSession, $endSession);
            });

        return $this->renderView('pages.planning.financial.session.page', [
            'pool' => $pool,
            'sessions' => $sessions,
            'startSessionId' => !$startSession ? 0 : $startSession->id,
            'endSessionId' => !$endSession ? 0 : $endSession->id,
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
    public function start()
    {
        $pool = $this->stash()->get('planning.financial.pool');
        $session = $this->poolService->getPoolStartSession($pool);
        $this->page($this->getSessionPageNumber($session));
    }

    /**
     * Go to the page of the current end session
     */
    public function end()
    {
        $pool = $this->stash()->get('planning.financial.pool');
        $session = $this->poolService->getPoolEndSession($pool);
        $this->page($this->getSessionPageNumber($session));
    }
}
