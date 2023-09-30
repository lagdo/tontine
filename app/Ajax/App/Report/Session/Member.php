<?php

namespace App\Ajax\App\Report\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Report\MemberService;

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
     *
     * @return void
     */
    public function show(SessionModel $session, MemberModel $member)
    {
        $this->deposits($session, $member);
        $this->remitments($session, $member);
        $this->loans($session, $member);
        $this->debts($session, $member);
        $this->fees($session, $member);
        $this->fines($session, $member);
        $this->fundings($session, $member);
        $this->disbursements($session, $member);
        // Empty the profits section.
        $this->response->html('report-profits', '');
    }

    private function deposits(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.deposits', [
            'receivables' => $this->memberService->getReceivables($session, $member),
        ]);
        $this->response->html('report-deposits', $html);
    }

    private function remitments(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.remitments', [
            'payables' => $this->memberService->getPayables($session, $member),
            'auctions' => $this->memberService->getAuctions($session, $member),
        ]);
        $this->response->html('report-remitments', $html);
    }

    private function loans(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.loans', [
            'loans' => $this->memberService->getLoans($session, $member),
        ]);
        $this->response->html('report-loans', $html);
    }

    private function debts(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.debts', [
            'debts' => $this->memberService->getDebts($session, $member),
        ]);
        $this->response->html('report-refunds', $html);
    }

    private function fees(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.fees', [
            'bills' => $this->memberService->getFees($session, $member),
        ]);
        $this->response->html('report-fees', $html);
    }

    private function fines(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.fines', [
            'bills' => $this->memberService->getFines($session, $member),
        ]);
        $this->response->html('report-fines', $html);
    }

    private function fundings(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.fundings', [
            'fundings' => $this->memberService->getFundings($session, $member),
        ]);
        $this->response->html('report-fundings', $html);
    }

    private function disbursements(SessionModel $session, MemberModel $member)
    {
        $html = $this->view()->render('tontine.pages.report.session.member.disbursements', [
            'disbursements' => $this->memberService->getDisbursements($session, $member),
        ]);
        $this->response->html('report-disbursements', $html);
    }
}
