<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Base\Round\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Presence\PresenceService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Before('getMember')]
#[Databag('meeting.presence')]
class SessionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.presence', 'session.page'];

    /**
     * @param SessionService $sessionService
     * @param PresenceService $presenceService
     */
    public function __construct(private SessionService $sessionService,
        private PresenceService $presenceService)
    {}

    protected function getMember()
    {
        $memberId = $this->bag('meeting.presence')->get('member.id', 0);
        $member = $memberId === 0 ? null :
            $this->presenceService->getMember($this->round(), $memberId);
        $this->stash()->set('presence.member', $member);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->presenceService->getSessionCount($this->round());
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        // Is null when showing presences by sessions.
        $member = $this->stash()->get('presence.member');
        return $this->renderView('pages.meeting.presence.session.page', [
            'member' => $member,
            'sessions' => $this->presenceService->getSessions($this->round(), $this->currentPage()),
            'absences' => !$member ? null :
                $this->presenceService->getMemberAbsences($this->round(), $member),
            'statuses' => $this->sessionService->getSessionStatuses(),
            'memberCount' => $this->presenceService->getMemberCount($this->round()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-presence-sessions');
    }
}
