<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;
use Stringable;

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
        $this->stash()->set('presence.member', $member);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable|string
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $member = $this->stash()->get('presence.member'); // Is null when showing presences by sessions.
        if($exchange && !$member)
        {
            return '';
        }

        return $this->renderView('pages.meeting.presence.session.home', [
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
        $member = $this->stash()->get('presence.member'); // Is null when showing presences by sessions.
        if($member !== null)
        {
            $this->response->js('Tontine')->showSmScreen('content-presence-right', 'presence-sm-screens');
        }
    }

    public function togglePresence(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
        $member = $this->stash()->get('presence.member');
        if(!$session || !$member)
        {
            return $this->response;
        }

        $this->presenceService->togglePresence($session, $member);
        $this->cl(MemberPage::class)->page();

        return $this->render();
    }
}
