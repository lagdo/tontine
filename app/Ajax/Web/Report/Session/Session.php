<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\ProfitService;
use Siak\Tontine\Service\Report\SessionService;

/**
 * @exclude
 */
class Session extends CallableClass
{
    /**
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * @var ProfitService
     */
    protected ProfitService $profitService;

    /**
     * @param SessionService $sessionService
     * @param ProfitService $profitService
     */
    public function __construct(SessionService $sessionService, ProfitService $profitService)
    {
        $this->sessionService = $sessionService;
        $this->profitService = $profitService;
    }

    /**
     * @param SessionModel $session
     *
     * @return void
     */
    public function show(SessionModel $session)
    {
        $this->deposits($session);
        $this->remitments($session);
        $this->loans($session);
        $this->refunds($session);
        $this->sessionBills($session);
        $this->totalBills($session);
        $this->fundings($session);
        $this->disbursements($session);
        $this->profits($session);
    }

    private function deposits(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.deposits', [
            'pools' => $this->sessionService->getReceivables($session),
        ]);
        $this->response->html('report-deposits', $html);
    }

    private function remitments(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.remitments', [
            'pools' => $this->sessionService->getPayables($session),
            'auctions' => $this->sessionService->getAuctions($session),
        ]);
        $this->response->html('report-remitments', $html);
    }

    private function loans(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.loans', [
            'loan' => $this->sessionService->getLoan($session),
        ]);
        $this->response->html('report-loans', $html);
    }

    private function refunds(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.refunds', [
            'refund' => $this->sessionService->getRefund($session),
        ]);
        $this->response->html('report-refunds', $html);
    }

    private function sessionBills(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.bills', [
            'title' => trans('tontine.report.titles.bills.session'),
            'charges' => $this->sessionService->getSessionCharges($session),
        ]);
        $this->response->html('report-session-bills', $html);
    }

    private function totalBills(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.bills', [
            'title' => trans('tontine.report.titles.bills.total'),
            'charges' => $this->sessionService->getTotalCharges($session),
        ]);
        $this->response->html('report-total-bills', $html);
    }

    private function fundings(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.fundings', [
            'funding' => $this->sessionService->getFunding($session),
        ]);
        $this->response->html('report-fundings', $html);
    }

    private function disbursements(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.report.session.session.disbursements', [
            'disbursement' => $this->sessionService->getDisbursement($session),
        ]);
        $this->response->html('report-disbursements', $html);
    }

    private function profits(SessionModel $session)
    {
        // Show the profits only if they were saved on this session.
        $profitSessionId = $session->round->properties['profit']['session'] ?? 0;
        $html = $profitSessionId !== $session->id ? '' :
            $this->view()->render('tontine.pages.report.session.session.profits', [
                'fundings' => $this->profitService->getDistributions($session),
                'profitAmount' => $session->round->properties['profit']['amount'] ?? 0,
            ]);
        $this->response->html('report-profits', $html);
    }
}