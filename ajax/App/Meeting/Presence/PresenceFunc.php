<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Presence\PresenceService;

/**
 * @databag meeting.presence
 * @before checkHostAccess ["meeting", "presences"]
 */
class PresenceFunc extends FuncComponent
{
    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    public function selectSession(int $sessionId): void
    {
        $this->bag('meeting.presence')->set('session.id', $sessionId);
        $this->bag('meeting.presence')->set('member.id', 0);
        $this->bag('meeting.presence')->set('member.page', 1);

        $round = $this->stash()->get('tenant.round');
        $session = $this->presenceService->getSession($round, $sessionId);
        $this->stash()->set('presence.session', $session);

        $this->cl(Member::class)->render();
    }

    public function selectMember(int $memberId): void
    {
        $this->bag('meeting.presence')->set('member.id', $memberId);
        $this->bag('meeting.presence')->set('session.id', 0);
        $this->bag('meeting.presence')->set('session.page', 1);

        $round = $this->stash()->get('tenant.round');
        $member = $this->presenceService->getMember($round, $memberId);
        $this->stash()->set('presence.member', $member);

        $this->cl(Session::class)->render();
    }

    public function exchange(): void
    {
        $exchange = $this->bag('meeting.presence')->get('exchange', false);
        $this->bag('meeting.presence')->set('exchange', !$exchange);

        $this->cl(Presence::class)->render();
    }
}
