<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;

/**
 * @databag presence
 * @before getMember
 */
class SessionFunc extends FuncComponent
{
    /**
     * @param SessionService $sessionService
     * @param PresenceService $presenceService
     */
    public function __construct(private SessionService $sessionService,
        private PresenceService $presenceService)
    {}

    protected function getMember()
    {
        $memberId = $this->bag('presence')->get('member.id', 0);
        $member = $memberId === 0 ? null : $this->presenceService->getMember($memberId);
        $this->stash()->set('presence.member', $member);
    }

    public function togglePresence(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
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
