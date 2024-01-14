<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;

use function trans;

/**
 * @databag presence
 */
class Presence extends CallableClass
{
    /**
     * @before checkRoundSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.presences'));
        $html = $this->render('pages.meeting.presence.home');
        $this->response->html('content-home', $html);

        $this->cl(Presence\Session::class)->home();
    }
}
