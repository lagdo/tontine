<?php

namespace Ajax\App\Planning\Financial;

use Ajax\PageComponent;
use Siak\Tontine\Model\Session as SessionModel;
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

    /**
     * @param SessionModel $session
     * @param SessionModel $startSession
     * @param SessionModel $endSession
     *
     * @return void
     */
    private function isCandidate(SessionModel $session,
        SessionModel $startSession, SessionModel $endSession): void
    {
        $session->candidate = !$startSession || !$endSession ? false :
            $session->start_at->lte($endSession->start_at) &&
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
        $sessions = $this->sessionService
            ->getTontineSessions($this->currentPage(), orderAsc: false)
            ->each(function($session) use($startSession, $endSession) {
                $this->isCandidate($session, $startSession, $endSession);
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

        $sessionCount = $this->sessionService
            ->getTontineSessionCount($session, true, false);
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
