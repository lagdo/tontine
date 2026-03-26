<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\FuncComponent;
use Illuminate\Support\Collection;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Siak\Tontine\Service\Meeting\Session\SummaryService;

#[Before('checkHostAccess', ["report", "round"])]
#[Before('checkOpenedSessions')]
#[Before('getPools')]
class RoundFunc extends FuncComponent
{
    use Table\PoolTrait;

    /**
     * @param SessionService $sessionService
     * @param SummaryService $summaryService
     */
    public function __construct(private SessionService $sessionService,
        private SummaryService $summaryService)
    {}

    /**
     * @return Collection
     */
    private function getSessions(): Collection
    {
        $sessions = $this->sessionService->getSessions($this->round(), orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));
        $this->stash()->set('report.sessions', $sessions);
        return $sessions;
    }

    /**
     * @param int $sessionId
     *
     * @return SessionModel|null
     */
    private function getSession(int $sessionId): SessionModel|null
    {
        // Stash the round sessions.
        $this->getSessions();

        return $sessionId <= 0 ? null :
            $this->sessionService->getSession($this->round(), $sessionId);
    }

    /**
     * @param SessionModel $session
     *
     * @return void
     */
    private function renderTables(SessionModel $session): void
    {
        $this->stash()->set('report.session', $session);
        $this->view()->share('lastSession', $session);

        // Render the page header.
        $this->cl(Header::class)->set('content', 'tables')->render();
        $this->cl(RoundTables::class)->render();
    }

    /**
     * @param SessionModel $session
     *
     * @return void
     */
    private function renderGraphs(SessionModel $session): void
    {
        $this->stash()->set('report.session', $session);
        $this->view()->share('lastSession', $session);

        // Render the page header.
        $this->cl(Header::class)->set('content', 'graphs')->render();
        $this->cl(RoundGraphs::class)->render();
    }

    public function showTables()
    {
        $sessions = $this->getSessions();
        if($sessions->count() > 0)
        {
            $this->cl(Select::class)->set('content', 'tables')->render();
            $this->renderTables($sessions->first());
        }
    }

    public function showRoundTables(int $sessionId)
    {
        if(($session = $this->getSession($sessionId)) !== null)
        {
            $this->renderTables($session);
        }
    }

    public function showGraphs()
    {
        $sessions = $this->getSessions();
        if($sessions->count() > 0)
        {
            $this->cl(Select::class)->set('content', 'graphs')->render();
            $this->renderGraphs($sessions->first());
        }
    }

    public function showRoundGraphs(int $sessionId)
    {
        if(($session = $this->getSession($sessionId)) !== null)
        {
            $this->renderGraphs($session);
        }
    }
}
