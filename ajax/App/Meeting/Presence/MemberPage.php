<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\PresenceService;
use Stringable;

use function trim;

/**
 * @databag presence
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
        $this->cache()->set('presence.session', $session);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = trim($this->bag('presence')->get('member.search', ''));

        return $this->presenceService->getMemberCount($search);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache()->get('presence.session'); // Is null when showing presences by members.
        $search = trim($this->bag('presence')->get('member.search', ''));

        return $this->renderView('pages.meeting.presence.member.page', [
            'session' => $session,
            'search' => $search,
            'members' => $this->presenceService->getMembers($search, $this->pageNumber()),
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
        $this->response->js()->makeTableResponsive('content-page-members');
    }
}
