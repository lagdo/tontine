<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Report\SessionService;
use Siak\Tontine\Service\Tontine\FundService;
use Sqids\SqidsInterface;

use function Jaxon\pm;
use function route;

/**
 * @exclude
 */
class Session extends CallableClass
{
    /**
     * @param SessionService $sessionService
     * @param ProfitService $profitService
     * @param SqidsInterface $sqids
     */
    public function __construct(protected SessionService $sessionService,
        protected ProfitService $profitService, protected FundService $fundService,
        protected SqidsInterface $sqids)
    {}

    /**
     * @param SessionModel $session
     *
     * @return void
     */
    public function show(SessionModel $session)
    {
        $this->bag('report')->set('session.id', $session->id);
        // Route to session report export
        $this->jq('#btn-session-export')->attr('href', route('report.session',
            ['sessionId' => $this->sqids->encode([$session->id])]));

        $this->deposits($session);
        $this->remitments($session);
        $this->loans($session);
        $this->refunds($session);
        $this->sessionBills($session);
        $this->totalBills($session);
        $this->savings($session);
        $this->disbursements($session);
    }

    private function deposits(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.deposits', [
            'pools' => $this->sessionService->getReceivables($session),
        ]);
        $this->response->html('report-deposits', $html);
    }

    private function remitments(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.remitments', [
            'pools' => $this->sessionService->getPayables($session),
            'auctions' => $this->sessionService->getAuctions($session),
        ]);
        $this->response->html('report-remitments', $html);
    }

    private function loans(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.loans', [
            'loan' => $this->sessionService->getLoan($session),
        ]);
        $this->response->html('report-loans', $html);
    }

    private function refunds(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.refunds', [
            'refund' => $this->sessionService->getRefund($session),
        ]);
        $this->response->html('report-refunds', $html);
    }

    private function sessionBills(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.bills', [
            'title' => trans('tontine.report.titles.bills.session'),
            'charges' => $this->sessionService->getSessionCharges($session),
        ]);
        $this->response->html('report-session-bills', $html);
    }

    private function totalBills(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.bills', [
            'title' => trans('tontine.report.titles.bills.total'),
            'charges' => $this->sessionService->getTotalCharges($session),
        ]);
        $this->response->html('report-total-bills', $html);
    }

    private function savings(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.savings', [
            'saving' => $this->sessionService->getSaving($session),
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('report-savings', $html);
        $this->response->html('report-fund-savings', '');

        $fundId = pm()->select('report-savings-fund-id')->toInt();
        $this->jq('#btn-report-fund-savings')
            ->click($this->cl(Saving::class)->rq()->home($fundId));
    }

    private function disbursements(SessionModel $session)
    {
        $html = $this->render('pages.report.session.session.disbursements', [
            'disbursement' => $this->sessionService->getDisbursement($session),
        ]);
        $this->response->html('report-disbursements', $html);
    }
}
