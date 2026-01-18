<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Base\Round\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Presence\PresenceService;

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
        $sessionId = $this->bag('meeting.presence')->get('session.id', 0);
        $session = $sessionId === 0 ? null :
            $this->presenceService->getSession($this->round(), $sessionId);
        $this->stash()->set('presence.session', $session);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('meeting.presence')->get('member.search', '');
        return $this->presenceService->getMemberCount($this->round(), $search);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('presence.session'); // Is null when showing presences by members.
        $search = $this->bag('meeting.presence')->get('member.search', '');
        return $this->renderTpl('pages.meeting.presence.member.page', [
            'session' => $session,
            'search' => $search,
            'members' => $this->presenceService
                ->getMembers($this->round(), $search, $this->currentPage()),
            'absences' => !$session ? null :
                $this->presenceService->getSessionAbsences($session),
            'sessionCount' => $this->presenceService->getSessionCount($this->round()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-presence-members');
    }
}
