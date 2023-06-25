<?php

namespace App\Ajax\App\Balance\Meeting\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Balance\SessionService;

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
     * @param SessionService $sessionService
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
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
        $this->fees($session);
        $this->fines($session);
    }

    private function deposits(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.balance.session.deposits', [
            'pools' => $this->sessionService->getReceivables($session),
        ]);
        $this->response->html('member-deposits', $html);
    }

    private function remitments(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.balance.session.remitments', [
            'pools' => $this->sessionService->getPayables($session),
        ]);
        $this->response->html('member-remitments', $html);
    }

    private function loans(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.balance.session.loans', [
            'loan' => $this->sessionService->getLoan($session),
        ]);
        $this->response->html('member-loans', $html);
    }

    private function refunds(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.balance.session.refunds', [
            'refund' => $this->sessionService->getRefund($session),
        ]);
        $this->response->html('member-refunds', $html);
    }

    private function fees(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.balance.session.fees', [
            'fees' => $this->sessionService->getFees($session),
        ]);
        $this->response->html('member-fees', $html);
    }

    private function fines(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.balance.session.fines', [
            'fines' => $this->sessionService->getFines($session),
        ]);
        $this->response->html('member-fines', $html);
    }
}
