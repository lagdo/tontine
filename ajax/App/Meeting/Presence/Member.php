<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Base\Round\Component;
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
        $sessionId = $this->bag('meeting.presence')->get('session.id', 0);
        $session = $sessionId === 0 ? null :
            $this->presenceService->getSession($this->round(), $sessionId);
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

        $search = $this->bag('meeting.presence')->get('member.search', '');
        return $this->renderView('pages.meeting.presence.member.home', [
            'session' => $session,
            'memberCount' => $this->presenceService->getMemberCount($this->round(), $search),
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
            $this->response->jo('tontine')
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
