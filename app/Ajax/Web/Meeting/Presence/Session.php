<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;

/**
 * @databag presence
 * @before getMember
 */
class Session extends Component
{
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
    public function html(): string
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $member = $this->cache->get('presence.member'); // Is null when showing presences by sessions.
        if($exchange && !$member)
        {
            return '';
        }

        return (string)$this->renderView('pages.meeting.presence.session.home', [
            'member' => $member,
            'sessionCount' => $this->presenceService->getSessionCount(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionPage::class)->page();
        $this->response->jw()->showSmScreen('content-home-sessions', 'presence-sm-screens');
    }

    public function togglePresence(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
        $member = $this->cache->get('presence.member');
        if(!$session || !$member)
        {
            return $this->response;
        }

        $this->presenceService->togglePresence($session, $member);
        $this->cl(MemberPage::class)->page();

        return $this->render();
    }
}
