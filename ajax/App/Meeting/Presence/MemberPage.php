<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Presence\PresenceService;
use Stringable;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Before('getSession')]
#[Databag('meeting.presence')]
class MemberPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.presence', 'member.page'];

    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    protected function getSession()
    {
        $round = $this->stash()->get('tenant.round');
        $sessionId = $this->bag('meeting.presence')->get('session.id', 0);
        $session = $sessionId === 0 ? null :
            $this->presenceService->getSession($round, $sessionId);
        $this->stash()->set('presence.session', $session);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('tenant.round');
        $search = $this->bag('meeting.presence')->get('member.search', '');
        return $this->presenceService->getMemberCount($round, $search);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('presence.session'); // Is null when showing presences by members.
        $search = $this->bag('meeting.presence')->get('member.search', '');
        return $this->renderView('pages.meeting.presence.member.page', [
            'session' => $session,
            'search' => $search,
            'members' => $this->presenceService
                ->getMembers($round, $search, $this->currentPage()),
            'absences' => !$session ? null :
                $this->presenceService->getSessionAbsences($session),
            'sessionCount' => $this->presenceService->getSessionCount($round),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-presence-members');
    }
}
