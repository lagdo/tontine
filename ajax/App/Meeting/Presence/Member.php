<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Tontine\MemberService;
use Stringable;

use function trim;

/**
 * @databag presence
 * @before getSession
 */
class Member extends Component
{
    /**
     * @param MemberService $memberService
     * @param PresenceService $presenceService
     */
    public function __construct(private MemberService $memberService,
        private PresenceService $presenceService)
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
    public function html(): Stringable|string
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $session = $this->cache()->get('presence.session'); // Is null when showing presences by members.
        if(!$exchange && !$session)
        {
            return '';
        }

        $search = trim($this->bag('presence')->get('member.search', ''));

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
        $this->response->jw()->showSmScreen('content-home-members', 'presence-sm-screens');
    }

    public function search(string $search)
    {
        $this->bag('presence')->set('member.search', trim($search));

        return $this->cl(MemberPage::class)->page();
    }

    public function togglePresence(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);
        $session = $this->cache()->get('presence.session');
        if(!$member || !$session)
        {
            return $this->response;
        }

        $this->presenceService->togglePresence($session, $member);
        $this->cl(SessionPage::class)->page();

        return $this->render();
    }
}
