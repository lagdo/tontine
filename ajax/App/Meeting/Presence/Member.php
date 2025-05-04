<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\PresenceService;
use Stringable;

/**
 * @databag presence
 * @before checkHostAccess ["meeting", "presences"]
 * @before getSession
 */
class Member extends Component
{
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
    public function html(): Stringable|string
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        // Is null when showing presences by members.
        $session = $this->stash()->get('presence.session');
        if(!$exchange && !$session)
        {
            return '';
        }

        $search = $this->bag('presence')->get('member.search', '');
        return $this->renderView('pages.meeting.presence.member.home', [
            'session' => $session,
            'memberCount' => $this->presenceService->getMemberCount($search),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
        // Is null when showing presences by members.
        $session = $this->stash()->get('presence.session');
        if($session !== null)
        {
            $this->response->js('Tontine')
                ->showSmScreen('content-presence-right', 'presence-sm-screens');
        }
    }

    public function search(string $search)
    {
        $this->bag('presence')->set('member.search', $search);
        $this->bag('presence')->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
