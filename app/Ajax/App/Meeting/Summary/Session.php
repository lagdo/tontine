<?php

namespace App\Ajax\App\Meeting\Summary;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Summary\SessionService;

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
     * @var SessionModel
     */
    private SessionModel $session;

    /**
     * @param SessionModel $session
     * @param boolean $isFinancial
     * @param SessionService $sessionService
     *
     * @return Response
     */
    public function show(SessionModel $session, bool $isFinancial, SessionService $sessionService)
    {
        $this->session = $session;
        $this->sessionService = $sessionService;

        $this->deposits();
        $this->remitments();
        if($isFinancial)
        {
            $this->fundings();
            $this->loans();
        }
        $this->fees();
        $this->fines();

        return $this->response;
    }

    private function deposits()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.deposits', [
            'pools' => $this->sessionService->getDeposits($this->session),
        ]);
        $this->response->html('member-deposits', $html);
    }

    private function remitments()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.remitments', [
            'pools' => $this->sessionService->getRemitments($this->session),
        ]);
        $this->response->html('member-remitments', $html);
    }

    private function fundings()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.fundings', [
            'funding' => $this->sessionService->getFunding($this->session),
        ]);
        $this->response->html('member-fundings', $html);
    }

    private function loans()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.loans', [
            'loan' => $this->sessionService->getLoan($this->session),
        ]);
        $this->response->html('member-loans', $html);

        $refund = $this->sessionService->getRefund($this->session);
        $html = $this->view()->render('tontine.pages.meeting.summary.session.refunds.principal', [
            'refund' => $refund,
        ]);
        $this->response->html('member-refunds-principal', $html);

        $html = $this->view()->render('tontine.pages.meeting.summary.session.refunds.interest', [
            'refund' => $refund,
        ]);
        $this->response->html('member-refunds-interest', $html);
    }

    private function fees()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.fees', [
            'fees' => $this->sessionService->getFees($this->session),
        ]);
        $this->response->html('member-fees', $html);
    }

    private function fines()
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.fines', [
            'fines' => $this->sessionService->getFines($this->session),
        ]);
        $this->response->html('member-fines', $html);
    }
}
