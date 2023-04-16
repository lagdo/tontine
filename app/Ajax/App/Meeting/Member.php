<?php

namespace App\Ajax\App\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\MemberService;
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

    /**
     * @di
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @var SessionModel
     */
    private SessionModel $session;

    /**
     * @var MemberModel
     */
    private MemberModel $member;

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
        $tontine = $this->sessionService->getTontine();

        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->view()->render('tontine.pages.meeting.member.home',
            compact('sessions', 'members', 'tontine'));
        $this->response->html('content-home', $html);

        $this->jq('#btn-members-refresh')->click($this->rq()->home());
        $memberId = pm()->select('select-member')->toInt();
        $sessionId = pm()->select('select-session')->toInt();
        $this->jq('#btn-member-select')->click($this->rq()->page($memberId, $sessionId));

        return $this->page($members->keys()->first(), $sessions->keys()->first());
    }

    public function page(int $memberId, int $sessionId)
    {
        $this->member = $this->sessionService->getMember($memberId);
        $this->session = $this->sessionService->getSession($sessionId);
        $tontine = $this->sessionService->getTontine();

        $this->deposits();
        $this->remitments();
        if($tontine->is_financial)
        {
            $this->fundings();
            $this->loans();
            $this->principalRefunds();
            $this->interestRefunds();
        }
        $this->fees();
        $this->fines();

        return $this->response;
    }

    public function deposits()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.deposits', [
            'subscriptions' => $this->memberService->getDeposits($this->member, $this->session),
        ]);
        $this->response->html('member-deposits', $html);

        return $this->response;
    }

    public function remitments()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.remitments', [
            'subscriptions' => $this->memberService->getRemitments($this->member, $this->session),
        ]);
        $this->response->html('member-remitments', $html);

        return $this->response;
    }

    public function fundings()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.fundings', [
            'fundings' => $this->memberService->getFundings($this->member, $this->session),
        ]);
        $this->response->html('member-fundings', $html);

        return $this->response;
    }

    public function loans()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.loans', [
            'loans' => $this->memberService->getLoans($this->member, $this->session),
        ]);
        $this->response->html('member-loans', $html);

        return $this->response;
    }

    public function principalRefunds()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.refunds.principal', [
            'loans' => $this->memberService->getprincipalRefunds($this->member, $this->session),
        ]);
        $this->response->html('member-refunds-principal', $html);

        return $this->response;
    }

    public function interestRefunds()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.refunds.interest', [
            'loans' => $this->memberService->getInterestRefunds($this->member, $this->session),
        ]);
        $this->response->html('member-refunds-interest', $html);

        return $this->response;
    }

    public function fees()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.fees', [
            'fees' => $this->memberService->getFees($this->member, $this->session),
        ]);
        $this->response->html('member-fees', $html);

        return $this->response;
    }

    public function fines()
    {
        $html = $this->view()->render('tontine.pages.meeting.member.fines', [
            'fines' => $this->memberService->getFines($this->member, $this->session),
        ]);
        $this->response->html('member-fines', $html);

        return $this->response;
    }
}
