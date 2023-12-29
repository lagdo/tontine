<?php

namespace App\Ajax\Web\Report;

use App\Ajax\CallableClass;
use App\Ajax\Web\Tontine\Options;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;

use function count;
use function Jaxon\pm;

/**
 * @databag report
 */
class Session extends CallableClass
{
    /**
     * @param TenantService $tenantService
     * @param TontineService $tontineService
     * @param SessionService $sessionService
     * @param ReportService $reportService
     */
    public function __construct(protected TenantService $tenantService,
        protected TontineService $tontineService, protected SessionService $sessionService,
        protected MemberService $memberService, protected ReportService $reportService)
    {}

    /**
     * @after hideMenuOnMobile
     */
    public function home()
    {
        // Don't show the page if there is no session or no member.
        $sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => $session->opened || $session->closed);
        if($sessions->count() === 0)
        {
            return $this->response;
        }
        $members = $this->memberService->getMemberList();
        if($members->count() === 0)
        {
            return $this->response;
        }
        $members->prepend('', 0);

        $this->response->html('section-title', trans('tontine.menus.report'));
        $html = $this->render('pages.report.session.home',
            ['sessions' => $sessions->pluck('title', 'id'), 'members' => $members]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-members-refresh')->click($this->rq()->home());
        $sessionId = pm()->select('select-session')->toInt();
        $memberId = pm()->select('select-member')->toInt();
        $this->jq('#btn-session-select')->click($this->rq()->showSession($sessionId));
        $this->jq('#btn-member-select')->click($this->rq()->showMember($sessionId, $memberId));

        return $this->_showSession($sessions->first());
    }

    private function showReportButtons(SessionModel $session)
    {
        $closings = $this->reportService->getClosings($session);
        $html = $this->render('pages.report.session.exports',
            ['sessionId' => $session->id, 'hasClosing' => count($closings) > 0]);
        $this->response->html('session-reports-export', $html);
        $this->jq('#btn-tontine-options')->click($this->cl(Options::class)->rq()->editOptions());

        return $this->response;
    }

    private function _showSession(SessionModel $session)
    {
        $this->response->html('session-report-title', $session->title);
        $this->cl(Session\Session::class)->show($session);

        return $this->showReportButtons($session);
    }

    public function showSession(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }

        return $this->_showSession($session);
    }

    public function showMember(int $sessionId, int $memberId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }
        if(!($member = $this->memberService->getMember($memberId)))
        {
            return $this->response;
        }

        $this->response->html('session-report-title', $session->title . ' - ' . $member->name);
        $this->cl(Session\Member::class)->show($session, $member);

        return $this->showReportButtons($session);
    }
}
