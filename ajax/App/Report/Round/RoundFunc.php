<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\FuncComponent;
use Illuminate\Support\Collection;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Before('checkHostAccess', ["report", "round"])]
#[Before('checkOpenedSessions')]
#[Before('getPools')]
class RoundFunc extends FuncComponent
{
    /**
     * @param SessionService $sessionService
     */
    public function __construct(private SessionService $sessionService)
    {}

    /**
     * @param int $sessionId
     *
     * @return SessionModel|null
     */
    private function getSession(int $sessionId): SessionModel|null
    {
        return $sessionId <= 0 ? null :
            $this->sessionService->getSession($this->round(), $sessionId);
    }

    /**
     * @return Collection
     */
    private function getSessions(): Collection
    {
        return $this->sessionService->getSessions($this->round(), orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));
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
        $this->cl(Action::class)->set('content', 'tables')->render();
        $this->cl(RoundTables::class)->render();
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
}
