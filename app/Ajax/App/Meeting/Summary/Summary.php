<?php

namespace App\Ajax\App\Meeting\Summary;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\Summary\MemberService as MemberSummaryService;
use Siak\Tontine\Service\Meeting\Summary\SessionService as SessionSummaryService;
use Siak\Tontine\Service\Meeting\SessionService;

use function compact;
use function Jaxon\pm;

class Summary extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * @di
     * @var MemberSummaryService
     */
    protected MemberSummaryService $memberSummaryService;

    /**
     * @di
     * @var SessionSummaryService
     */
    protected SessionSummaryService $sessionSummaryService;

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
        $html = $this->view()->render('tontine.pages.meeting.summary.home',
            compact('sessions', 'members', 'tontine'));
        $this->response->html('content-home', $html);

        $this->jq('#btn-members-refresh')->click($this->rq()->home());
        $sessionId = pm()->select('select-session')->toInt();
        $memberId = pm()->select('select-member')->toInt();
        $this->jq('#btn-member-select')->click($this->rq()->show($sessionId, $memberId));

        return $this->show($sessions->keys()->first(), $members->keys()->first());
    }

    public function show(int $sessionId, int $memberId)
    {
        $tontine = $this->sessionService->getTontine();
        $session = $this->sessionService->getSession($sessionId);
        if($memberId === 0)
        {
            return $this->cl(Session::class)->show($session, $tontine->is_financial, $this->sessionSummaryService);
        }

        $member = $this->sessionService->getMember($memberId);
        return $this->cl(Member::class)->show($session, $member, $tontine->is_financial, $this->memberSummaryService);
    }
}
