<?php

namespace App\Ajax\Web\Report;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\TontineService;

use function compact;
use function Jaxon\pm;

class Session extends CallableClass
{
    /**
     * @di
     * @var TontineService
     */
    protected TontineService $tontineService;

    /**
     * @di
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @after hideMenuOnMobile
     */
    public function home()
    {
        // Don't show the page if there is no session or no member.
        $sessions = $this->tenantService->getSessions(orderAsc: false)
            ->filter(function($session) {
                return $session->opened || $session->closed;
            })
            ->pluck('title', 'id');
        if($sessions->count() === 0)
        {
            return $this->response;
        }
        $members = $this->tontineService->getMembers();
        if($members->count() === 0)
        {
            return $this->response;
        }
        $members->prepend('', 0);

        $this->response->html('section-title', trans('tontine.menus.report'));
        $html = $this->view()->render('tontine.pages.report.session.home',
            compact('sessions', 'members'));
        $this->response->html('content-home', $html);

        $this->jq('#btn-members-refresh')->click($this->rq()->home());
        $sessionId = pm()->select('select-session')->toInt();
        $memberId = pm()->select('select-member')->toInt();
        $this->jq('#btn-session-select')->click($this->rq()->showSession($sessionId));
        $this->jq('#btn-member-select')->click($this->rq()->showMember($sessionId, $memberId));

        $session = $this->tenantService->getSession($sessions->keys()->first());
        $this->response->html('session-report-title', $session->title);
        $this->cl(Session\Session::class)->show($session);

        return $this->response;
    }

    public function showSession(int $sessionId)
    {
        if(!($session = $this->tenantService->getSession($sessionId)))
        {
            return $this->response;
        }

        $this->response->html('session-report-title', $session->title);
        $this->cl(Session\Session::class)->show($session);

        return $this->response;
    }

    public function showMember(int $sessionId, int $memberId)
    {
        if(!($session = $this->tenantService->getSession($sessionId)))
        {
            return $this->response;
        }
        if(!($member = $this->tontineService->getMember($memberId)))
        {
            return $this->response;
        }

        $this->response->html('session-report-title', $session->title . ' - ' . $member->name);
        $this->cl(Session\Member::class)->show($session, $member);

        return $this->response;
    }
}
