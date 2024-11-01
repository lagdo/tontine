<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;

/**
 * @databag presence
 * @before getMember
 */
class SessionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['presence', 'session.page'];

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
        $this->cache->set('presence.member', $member);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->presenceService->getSessionCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $member = $this->cache->get('presence.member'); // Is null when showing presences by sessions.

        return (string)$this->renderView('pages.meeting.presence.session.page', [
            'member' => $member,
            'sessions' => $this->presenceService->getSessions($this->page),
            'absences' => !$member ? null :
                $this->presenceService->getMemberAbsences($member),
            'statuses' => $this->sessionService->getSessionStatuses(),
            'memberCount' => $this->presenceService->getMemberCount(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page-sessions');
    }
}
