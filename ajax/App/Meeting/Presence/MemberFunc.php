<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Presence\PresenceService;

/**
 * @databag meeting.presence
 * @before checkHostAccess ["meeting", "presences"]
 * @before getSession
 */
class MemberFunc extends FuncComponent
{
    /**
     * @param MemberService $memberService
     * @param PresenceService $presenceService
     */
    public function __construct(private MemberService $memberService,
        private PresenceService $presenceService)
    {}

    protected function getSession()
    {
        $round = $this->stash()->get('tenant.round');
        $sessionId = $this->bag('meeting.presence')->get('session.id', 0);
        $session = $sessionId === 0 ? null :
            $this->presenceService->getSession($round, $sessionId);
        $this->stash()->set('presence.session', $session);
    }

    public function togglePresence(int $memberId)
    {
        $round = $this->stash()->get('tenant.round');
        $member = $this->memberService->getMember($round, $memberId);
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
