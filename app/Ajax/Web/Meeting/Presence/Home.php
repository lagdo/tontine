<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\PresenceService;

use function trans;

/**
 * @databag presence
 */
/**
 * @before checkGuestAccess ["meeting", "presences"]
 */
class Home extends CallableClass
{
    /**
     * @var PresenceService
     */
    protected PresenceService $presenceService;

    /**
     * @before checkRoundSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $exchange = $this->bag('presence')->get('exchange', false);

        $this->response->html('section-title', trans('tontine.menus.presences'));
        $html = $this->renderView('pages.meeting.presence.home', ['exchange' => $exchange]);
        $this->response->html('content-home', $html);

        $this->bag('presence')->set('session.id', 0);
        $this->bag('presence')->set('member.id', 0);

        return $this->cl(!$exchange ? Session::class : Member::class)->home();
    }

    public function exchange()
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $this->bag('presence')->set('exchange', !$exchange);

        return $this->home();
    }

    /**
     * @di $presenceService
     */
    public function selectSession(int $sessionId)
    {
        if(!($session = $this->presenceService->getSession($sessionId)))
        {
            // Todo: show en error message
            return $this->response;
        }

        return $this->cl(Member::class)->show($session);
    }

    /**
     * @di $presenceService
     */
    public function selectMember(int $memberId)
    {
        if(!($member = $this->presenceService->getMember($memberId)))
        {
            // Todo: show en error message
            return $this->response;
        }

        return $this->cl(Session::class)->show($member);
    }
}
