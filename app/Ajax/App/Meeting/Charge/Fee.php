<?php

namespace App\Ajax\App\Meeting\Charge;

use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Service\Charge\FeeReportService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Fee extends CallableClass
{
    /**
     * @di
     * @var FeeService
     */
    protected FeeService $feeService;

    /**
     * @di
     * @var FeeReportService
     */
    protected FeeReportService $reportService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->feeService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show($session, $feeService, $reportService)
    {
        $this->session = $session;
        $this->feeService = $feeService;
        $this->reportService = $reportService;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.fee.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees', $html);
        $this->jq('#btn-fees-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $fees = $this->feeService->getFees($this->session, $pageNumber);
        $feeCount = $this->feeService->getFeeCount();
        // Settlement report
        $settlements = $this->reportService->getSettlements($this->session);
        // Bill counts
        $bills = $this->reportService->getBills($this->session);

        $html = $this->view()->render('tontine.pages.meeting.fee.page')
            ->with('session', $this->session)
            ->with('fees', $fees)
            ->with('settlements', $settlements['total'])
            ->with('bills', $bills['total'])
            ->with('zero', $this->reportService->getFormattedAmount(0))
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $feeCount));
        // if($this->session->closed)
        // {
        //     $html->with('report', $this->feeService->getFeesReport($this->session));
        // }
        $this->response->html('meeting-fees-page', $html);

        $feeId = jq()->parent()->attr('data-fee-id')->toInt();
        $this->jq('.btn-fee-settlements')->click($this->cl(Settlement::class)->rq()->home($feeId));

        return $this->response;
    }
}
