<?php

namespace App\Ajax\App\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\SessionService;

use function compact;
use function Jaxon\pm;

class Member extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    protected SessionService $sessionService;

    public function home()
    {
        $sessions = $this->sessionService->getSessions();
        if($sessions->count() === 0)
        {
            return $this->response;
        }
        $members = $this->sessionService->getMembers();
        if($members->count() === 0)
        {
            return $this->response;
        }

        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->view()->render('tontine.pages.meeting.member.home', compact('sessions', 'members'));
        $this->response->html('content-home', $html);

        $this->jq('#btn-members-refresh')->click($this->rq()->home());
        $memberId = pm()->select('select-member')->toInt();
        $sessionId = pm()->select('select-session')->toInt();
        $this->jq('#btn-member-select')->click($this->rq()->page($memberId, $sessionId));

        return $this->page($members->keys()->first(), $sessions->keys()->first());
    }

    public function page(int $memberId, int $sessionId)
    {
        return $this->response;
    }
}
