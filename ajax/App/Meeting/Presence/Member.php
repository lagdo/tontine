<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Presence\PresenceService;
use Stringable;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Before('getSession')]
#[Databag('meeting.presence')]
#[Export(base: ['render'])]
class Member extends Component
{
    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    protected function getSession(): void
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
    public function html(): Stringable|string
    {
        $exchange = $this->bag('meeting.presence')->get('exchange', false);
        // Is null when showing presences by members.
        $session = $this->stash()->get('presence.session');
        if(!$exchange && !$session)
        {
            return '';
        }

        $round = $this->stash()->get('tenant.round');
        $search = $this->bag('meeting.presence')->get('member.search', '');
        return $this->renderView('pages.meeting.presence.member.home', [
            'session' => $session,
            'memberCount' => $this->presenceService->getMemberCount($round, $search),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(MemberPage::class)->page();
        // Is null when showing presences by members.
        $session = $this->stash()->get('presence.session');
        if($session !== null)
        {
            $this->response->jo('Tontine')
                ->showSmScreen('content-presence-right', 'presence-sm-screens');
        }
    }

    public function search(string $search): void
    {
        $this->bag('meeting.presence')->set('member.search', $search);
        $this->bag('meeting.presence')->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
