<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;

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
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(MemberService $memberService, SessionService $sessionService)
    {
        $this->memberService = $memberService;
        $this->sessionService = $sessionService;
    }

    /**
     * @param SessionModel $session
     * @param MemberModel $member
     *
     * @return void
     */
    public function show(SessionModel $session, MemberModel $member)
    {
        $this->deposits($session, $member);
        $this->remitments($session, $member);
        $this->loans($session, $member);
        $this->refunds($session, $member);
        $this->sessionBills($session, $member);
        $this->totalBills($session, $member);
        $this->savings($session, $member);
        $this->disbursements($session, $member);
    }

    private function deposits(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.deposits', [
            'receivables' => $this->memberService->getReceivables($session, $member),
        ]);
        $this->response->html('report-deposits', $html);
    }

    private function remitments(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.remitments', [
            'payables' => $this->memberService->getPayables($session, $member),
            'auctions' => $this->memberService->getAuctions($session, $member),
        ]);
        $this->response->html('report-remitments', $html);
    }

    private function loans(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.loans', [
            'loans' => $this->memberService->getLoans($session, $member),
        ]);
        $this->response->html('report-loans', $html);
    }

    private function refunds(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.refunds', [
            'refunds' => $this->memberService->getRefunds($session, $member),
        ]);
        $this->response->html('report-refunds', $html);
    }

    private function sessionBills(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.bills.session', [
            'bills' => $this->memberService->getBills($session, $member),
        ]);
        $this->response->html('report-session-bills', $html);
    }

    private function totalBills(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.bills.total', [
            'charges' => $this->sessionService->getTotalCharges($session, $member),
        ]);
        $this->response->html('report-total-bills', $html);
    }

    private function savings(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.savings', [
            'savings' => $this->memberService->getSavings($session, $member),
        ]);
        $this->response->html('report-savings', $html);
        $this->response->html('report-fund-savings', '');
    }

    private function disbursements(SessionModel $session, MemberModel $member)
    {
        $html = $this->render('pages.report.session.member.disbursements', [
            'disbursements' => $this->memberService->getDisbursements($session, $member),
        ]);
        $this->response->html('report-disbursements', $html);
    }
}
