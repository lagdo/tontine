<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Presence\PresenceService;
use Stringable;

#[Before('checkHostAccess', ["meeting", "presences"])]
#[Before('getMember')]
#[Databag('meeting.presence')]
#[Export(base: ['render'])]
class Session extends Component
{
    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    protected function getMember()
    {
        $memberId = $this->bag('meeting.presence')->get('member.id', 0);
        $member = $memberId === 0 ? null :
            $this->presenceService->getMember($this->round(), $memberId);
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

        return $this->renderView('pages.meeting.presence.session.home', [
            'member' => $member,
            'sessionCount' => $this->presenceService->getSessionCount($this->round()),
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
            $this->response->jo('tontine')->showSmScreen('content-presence-right', 'presence-sm-screens');
        }
    }
}
