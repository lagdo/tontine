<?php

namespace App\Ajax\App\Balance\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Summary\MemberService;

/**
 * @exclude
 */
class Member extends CallableClass
{
    /**
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

    /**
     * @param SessionModel $session
     * @param MemberModel $member
     * @param boolean $isFinancial
     * @param MemberService $memberService
     *
     * @return Response
     */
    public function show(SessionModel $session, MemberModel $member, bool $isFinancial, MemberService $memberService)
    {
        $this->session = $session;
        $this->member = $member;
        $this->memberService = $memberService;

        $this->deposits();
        $this->remitments();
        if($isFinancial)
        {
            $this->loans();
            $this->debts();
        }
        $this->fees();
        $this->fines();

        return $this->response;
    }

    private function deposits()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.member.deposits', [
            'subscriptions' => $this->memberService->getDeposits($this->member, $this->session),
        ]);
        $this->response->html('member-deposits', $html);
    }

    private function remitments()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.member.remitments', [
            'subscriptions' => $this->memberService->getRemitments($this->member, $this->session),
        ]);
        $this->response->html('member-remitments', $html);
    }

    private function loans()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.member.loans', [
            'loans' => $this->memberService->getLoans($this->member, $this->session),
        ]);
        $this->response->html('member-loans', $html);
    }

    private function debts()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.member.debts', [
            'debts' => $this->memberService->getDebts($this->member, $this->session),
        ]);
        $this->response->html('member-refunds', $html);
    }

    private function fees()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.member.fees', [
            'fees' => $this->memberService->getFees($this->member, $this->session),
        ]);
        $this->response->html('member-fees', $html);
    }

    private function fines()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.member.fines', [
            'fines' => $this->memberService->getFines($this->member, $this->session),
        ]);
        $this->response->html('member-fines', $html);
    }
}
