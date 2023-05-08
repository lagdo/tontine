<?php

namespace App\Ajax\App\Balance\Meeting\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Balance\MemberService;

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
     * @param MemberService $memberService
     */
    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * @param SessionModel $session
     * @param MemberModel $member
     * @param boolean $isFinancial
     *
     * @return void
     */
    public function show(SessionModel $session, MemberModel $member, bool $isFinancial)
    {
        $this->deposits($session, $member);
        $this->remitments($session, $member);
        if($isFinancial)
        {
            $this->loans($session, $member);
            $this->debts($session, $member);
        }
        $this->fees($session, $member);
        $this->fines($session, $member);
    }

    private function deposits(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.balance.member.deposits', [
            'subscriptions' => $this->memberService->getReceivables($member, $session),
        ]);
        $this->response->html('member-deposits', $html);
    }

    private function remitments(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.balance.member.remitments', [
            'subscriptions' => $this->memberService->getPayables($member, $session),
        ]);
        $this->response->html('member-remitments', $html);
    }

    private function loans(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.balance.member.loans', [
            'loans' => $this->memberService->getLoans($member, $session),
        ]);
        $this->response->html('member-loans', $html);
    }

    private function debts(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.balance.member.debts', [
            'debts' => $this->memberService->getDebts($member, $session),
        ]);
        $this->response->html('member-refunds', $html);
    }

    private function fees(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.balance.member.fees', [
            'fees' => $this->memberService->getFees($member, $session),
        ]);
        $this->response->html('member-fees', $html);
    }

    private function fines(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.balance.member.fines', [
            'bills' => $this->memberService->getFineBills($member, $session),
        ]);
        $this->response->html('member-fines', $html);
    }
}
