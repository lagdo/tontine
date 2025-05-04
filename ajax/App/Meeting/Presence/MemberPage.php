<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\PresenceService;
use Stringable;

/**
 * @databag presence
 * @before checkHostAccess ["meeting", "presences"]
 * @before getSession
 */
class MemberPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['presence', 'member.page'];

    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    protected function getSession()
    {
        $sessionId = $this->bag('presence')->get('session.id', 0);
        $session = $sessionId === 0 ? null : $this->presenceService->getSession($sessionId);
        $this->stash()->set('presence.session', $session);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('presence')->get('member.search', '');

        return $this->presenceService->getMemberCount($search);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('presence.session'); // Is null when showing presences by members.
        $search = $this->bag('presence')->get('member.search', '');

        return $this->renderView('pages.meeting.presence.member.page', [
            'session' => $session,
            'search' => $search,
            'members' => $this->presenceService->getMembers($search, $this->currentPage()),
            'absences' => !$session ? null :
                $this->presenceService->getSessionAbsences($session),
            'sessionCount' => $this->presenceService->getSessionCount(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-presence-members');
    }
}
