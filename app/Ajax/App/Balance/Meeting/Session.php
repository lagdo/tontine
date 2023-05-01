<?php

namespace App\Ajax\App\Balance\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\SessionService;

use function compact;
use function Jaxon\pm;

class Session extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    protected SessionService $sessionService;

    public function home()
    {
        // Don't show the page if there is no session or no member.
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
        $members->prepend('', 0);

        $tontine = $this->sessionService->getTontine();
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->view()->render('tontine.pages.balance.home',
            compact('sessions', 'members', 'tontine'));
        $this->response->html('content-home', $html);

        $this->jq('#btn-members-refresh')->click($this->rq()->home());
        $sessionId = pm()->select('select-session')->toInt();
        $memberId = pm()->select('select-member')->toInt();
        $this->jq('#btn-member-select')->click($this->rq()->show($sessionId, $memberId));

        $session = $this->sessionService->getSession($sessions->keys()->first());
        $this->cl(Session\Session::class)->show($session, $tontine->is_financial);

        return $this->response;
    }

    public function show(int $sessionId, int $memberId)
    {
        $tontine = $this->sessionService->getTontine();
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }
        if($memberId === 0)
        {
            $this->cl(Session\Session::class)->show($session, $tontine->is_financial);
            return $this->response;
        }

        if(!($member = $this->sessionService->getMember($memberId)))
        {
            return $this->response;
        }
        $this->cl(Session\Member::class)->show($session, $member, $tontine->is_financial);
        return $this->response;
    }
}
