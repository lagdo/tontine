<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;

/**
 * @databag presence
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

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $member = $this->cl(Home::class)->getMember(); // Is null when showing presences by sessions.
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
        $member = $this->cl(Home::class)->getMember();
        if(!$session || !$member)
        {
            return $this->response;
        }

        $this->presenceService->togglePresence($session, $member);

        $this->cl(MemberPage::class)->page();
        return $this->render();
    }
}
