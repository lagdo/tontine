<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\Meeting\PresenceService;

use function trim;

/**
 * @databag presence
 * @before getSession
 */
class MemberFunc extends FuncComponent
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
        $this->stash()->set('presence.session', $session);
    }

    public function search(string $search)
    {
        $this->bag('presence')->set('member.search', trim($search));
        $this->cl(MemberPage::class)->page();
    }

    public function togglePresence(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);
        $session = $this->stash()->get('presence.session');
        if(!$member || !$session)
        {
            return;
        }

        $this->presenceService->togglePresence($session, $member);
        $this->cl(SessionPage::class)->page();
        $this->cl(Member::class)->render();
    }
}
