<?php

namespace App\Ajax\App\Meeting\Charge;

use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Service\Charge\FineReportService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Fine extends CallableClass
{
    /**
     * @di
     * @var FineService
     */
    protected FineService $fineService;

    /**
     * @di
     * @var FineReportService
     */
    protected FineReportService $reportService;

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
        $this->session = $this->fineService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show($session, $fineService, $reportService)
    {
        $this->session = $session;
        $this->fineService = $fineService;
        $this->reportService = $reportService;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.fine.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fines', $html);
        $this->jq('#btn-fines-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $fines = $this->fineService->getFines($this->session, $pageNumber);
        $fineCount = $this->fineService->getFineCount();
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
        $this->response->html('meeting-fines-page', $html);

        $fineId = jq()->parent()->attr('data-fine-id')->toInt();
        $this->jq('.btn-fine-add')->click($this->cl(Member::class)->rq()->home($fineId));
        $this->jq('.btn-fine-settlements')->click($this->cl(Settlement::class)->rq()->home($fineId));

        return $this->response;
    }
}
