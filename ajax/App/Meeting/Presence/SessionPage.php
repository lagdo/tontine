<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\PageComponent;
use Siak\Tontine\Service\Presence\PresenceService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

/**
 * @databag meeting.presence
 * @before checkHostAccess ["meeting", "presences"]
 * @before getMember
 */
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
        $round = $this->stash()->get('tenant.round');
        $memberId = $this->bag('meeting.presence')->get('member.id', 0);
        $member = $memberId === 0 ? null :
            $this->presenceService->getMember($round, $memberId);
        $this->stash()->set('presence.member', $member);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('tenant.round');
        return $this->presenceService->getSessionCount($round);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        // Is null when showing presences by sessions.
        $round = $this->stash()->get('tenant.round');
        $member = $this->stash()->get('presence.member');
        return $this->renderView('pages.meeting.presence.session.page', [
            'member' => $member,
            'sessions' => $this->presenceService->getSessions($round, $this->currentPage()),
            'absences' => !$member ? null :
                $this->presenceService->getMemberAbsences($round, $member),
            'statuses' => $this->sessionService->getSessionStatuses(),
            'memberCount' => $this->presenceService->getMemberCount($round),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-presence-sessions');
    }
}
