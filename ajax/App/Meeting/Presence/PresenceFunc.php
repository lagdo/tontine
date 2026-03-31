<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Base\Round\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Presence\PresenceService;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Databag('meeting.presence')]
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

        $session = $this->presenceService->getSession($this->round(), $sessionId);
        $this->stash()->set('presence.session', $session);

        $this->cl(Member::class)->render();
    }

    public function selectMember(int $memberId): void
    {
        $this->bag('meeting.presence')->set('member.id', $memberId);
        $this->bag('meeting.presence')->set('session.id', 0);
        $this->bag('meeting.presence')->set('session.page', 1);

        $member = $this->presenceService->getMember($this->round(), $memberId);
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
