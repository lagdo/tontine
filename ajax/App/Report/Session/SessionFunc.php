<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\FuncComponent;
use Illuminate\Support\Collection;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Before('checkHostAccess', ["report", "session"])]
#[Before('checkOpenedSessions')]
class SessionFunc extends FuncComponent
{
    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(private MemberService $memberService,
        private SessionService $sessionService)
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
        return $sessionId <= 0 ? null :
            $this->sessionService->getSession($this->round(), $sessionId);
    }

    /**
     * @param int $memberId
     *
     * @return MemberModel|null
     */
    private function getMember(int $memberId): MemberModel|null
    {
        return $memberId <= 0 ? null :
            $this->memberService->getMember($this->round(), $memberId);
    }

    /**
     * @param SessionModel $session
     * @param MemberModel|null $member
     *
     * @return void
     */
    private function renderTables(SessionModel $session, ?MemberModel $member = null): void
    {
        $this->stash()->set('report.session', $session);
        $this->stash()->set('report.member', $member);

        // Render the page header.
        $this->cl(Header::class)->set('content', 'tables')->render();
        $this->cl(SessionTables::class)->render();
    }

    /**
     * @param SessionModel $session
     *
     * @return void
     */
    private function renderGraphs(SessionModel $session): void
    {
        $this->stash()->set('report.session', $session);
        $this->stash()->set('report.member', null);

        // Render the page header.
        $this->cl(Header::class)->set('content', 'graphs')->render();
        $this->cl(SessionGraphs::class)->render();
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

    public function showSessionTables(int $sessionId)
    {
        if(($session = $this->getSession($sessionId)) !== null)
        {
            $this->getSessions();
            $this->renderTables($session);
        }
    }

    public function showMemberTables(int $sessionId, int $memberId)
    {
        if(($session = $this->getSession($sessionId)) !== null && 
            ($member = $this->getMember($memberId)) !== null)
        {
            $this->renderTables($session, $member);
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

    public function showSessionGraphs(int $sessionId)
    {
        if(($session = $this->getSession($sessionId)) !== null)
        {
            $this->getSessions();
            $this->renderGraphs($session);
        }
    }
}
