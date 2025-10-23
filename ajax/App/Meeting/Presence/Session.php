<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Presence\PresenceService;
use Stringable;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Before('getMember')]
#[Databag('meeting.presence')]
class Session extends Component
{
    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    protected function getMember()
    {
        $round = $this->stash()->get('tenant.round');
        $memberId = $this->bag('meeting.presence')->get('member.id', 0);
        $member = $memberId === 0 ? null :
            $this->presenceService->getMember($round, $memberId);
        $this->stash()->set('presence.member', $member);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable|string
    {
        $exchange = $this->bag('meeting.presence')->get('exchange', false);
        $member = $this->stash()->get('presence.member'); // Is null when showing presences by sessions.
        if($exchange && !$member)
        {
            return '';
        }

        $round = $this->stash()->get('tenant.round');
        return $this->renderView('pages.meeting.presence.session.home', [
            'member' => $member,
            'sessionCount' => $this->presenceService->getSessionCount($round),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SessionPage::class)->page();
        $member = $this->stash()->get('presence.member'); // Is null when showing presences by sessions.
        if($member !== null)
        {
            $this->response->jo('Tontine')->showSmScreen('content-presence-right', 'presence-sm-screens');
        }
    }
}
