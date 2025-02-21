<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Meeting\PresenceService;

/**
 * @databag presence
 * @before checkHostAccess ["meeting", "presences"]
 */
class PresenceFunc extends FuncComponent
{
    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    public function exchange()
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $this->bag('presence')->set('exchange', !$exchange);

        $this->cl(Presence::class)->home();
    }

    public function selectSession(int $sessionId)
    {
        $this->bag('presence')->set('session.id', $sessionId);
        $this->bag('presence')->set('member.id', 0);
        $this->bag('presence')->set('member.page', 1);

        $this->stash()->set('presence.session', $this->presenceService->getSession($sessionId));

        $this->cl(Member::class)->render();
    }

    public function selectMember(int $memberId)
    {
        $this->bag('presence')->set('member.id', $memberId);
        $this->bag('presence')->set('session.id', 0);
        $this->bag('presence')->set('session.page', 1);

        $this->stash()->set('presence.member', $this->presenceService->getMember($memberId));

        $this->cl(Session::class)->render();
    }
}
