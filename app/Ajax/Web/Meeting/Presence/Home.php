<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\PresenceService;

use function trans;

/**
 * @databag presence
 * @before checkGuestAccess ["meeting", "presences"]
 */
class Home extends CallableClass
{
    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    /**
     * @exclude
     */
    public function getSession(): ?SessionModel
    {
        if(($sessionId = $this->bag('presence')->get('session.id', 0)) === 0)
        {
            return null;
        }
        return $this->presenceService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function getMember(): ?MemberModel
    {
        if(($memberId = $this->bag('presence')->get('member.id', 0)) === 0)
        {
            return null;
        }
        return $this->presenceService->getMember($memberId);
    }

    /**
     * @before checkRoundSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->bag('presence')->set('session.id', 0);
        $this->bag('presence')->set('member.id', 0);
        $exchange = $this->bag('presence')->get('exchange', false);

        $this->response->html('section-title', trans('tontine.menus.presences'));
        $html = $this->renderView('pages.meeting.presence.home', [
            'exchange' => $exchange,
        ]);
        $this->response->html('content-home', $html);

        $clAtLeft = !$exchange ? $this->cl(Session::class) : $this->cl(Member::class);
        $clAtLeft->render();

        return $this->response;
    }

    public function exchange()
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $this->bag('presence')->set('exchange', !$exchange);

        return $this->home();
    }

    public function selectSession(int $sessionId)
    {
        $this->bag('presence')->set('session.id', $sessionId);
        $this->bag('presence')->set('member.id', 0);
        $this->bag('presence')->set('member.page', 1);

        return $this->cl(Member::class)->render();
    }

    public function selectMember(int $memberId)
    {
        $this->bag('presence')->set('member.id', $memberId);
        $this->bag('presence')->set('session.id', 0);
        $this->bag('presence')->set('session.page', 1);

        return $this->cl(Session::class)->render();
    }
}
