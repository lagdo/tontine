<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Base\Round\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Presence\PresenceService;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Before('getSession')]
#[Databag('meeting.presence')]
class MemberFunc extends FuncComponent
{
    /**
     * @param MemberService $memberService
     * @param PresenceService $presenceService
     */
    public function __construct(private MemberService $memberService,
        private PresenceService $presenceService)
    {}

    protected function getSession(): void
    {
        $sessionId = $this->bag('meeting.presence')->get('session.id', 0);
        $session = $sessionId === 0 ? null :
            $this->presenceService->getSession($this->round(), $sessionId);
        $this->stash()->set('presence.session', $session);
    }

    public function togglePresence(int $memberId): void
    {
        $member = $this->memberService->getMember($this->round(), $memberId);
        $session = $this->stash()->get('presence.session');
        if(!$member || !$session)
        {
            return;
        }

        $this->presenceService->togglePresence($session, $member);
        $this->cl(SessionPage::class)->page();
        $this->cl(Member::class)->render();
    }
}
