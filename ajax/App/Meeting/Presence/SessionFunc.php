<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Presence\PresenceService;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Before('getMember')]
#[Databag('meeting.presence')]
class SessionFunc extends FuncComponent
{
    /**
     * @param SessionService $sessionService
     * @param PresenceService $presenceService
     */
    public function __construct(private SessionService $sessionService,
        private PresenceService $presenceService)
    {}

    protected function getMember(): void
    {
        $round = $this->stash()->get('tenant.round');
        $memberId = $this->bag('meeting.presence')->get('member.id', 0);
        $member = $memberId === 0 ? null :
            $this->presenceService->getMember($round, $memberId);
        $this->stash()->set('presence.member', $member);
    }

    public function togglePresence(int $sessionId): void
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->sessionService->getSession($round, $sessionId);
        $member = $this->stash()->get('presence.member');
        if(!$session || !$member)
        {
            return;
        }

        $this->presenceService->togglePresence($session, $member);
        $this->cl(MemberPage::class)->page();
        $this->cl(Session::class)->render();
    }
}
