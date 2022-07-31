<?php

namespace App\Ajax\App\Meeting\Charge;

use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\FeeSettlementService;
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
     * @var MeetingService
     */
    protected MeetingService $meetingService;

    /**
     * @di
     * @var FeeSettlementService
     */
    protected FeeSettlementService $settlementService;

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
        $this->session = $this->meetingService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show($session, $meetingService, $settlementService)
    {
        $this->session = $session;
        $this->meetingService = $meetingService;
        $this->settlementService = $settlementService;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('pages.meeting.fee.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees', $html);
        $this->jq('#btn-fees-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $fees = $this->meetingService->getFees($this->session, $pageNumber);
        $feeCount = $this->meetingService->getFeeCount();
        // Settlement counts
        $settlements = [
            'current' => $this->settlementService->getSettlementCount($this->session, false),
            'previous' => $this->settlementService->getSettlementCount($this->session, true),
        ];
        // Bill counts
        $bills = [
            'current' => $this->settlementService->getBillCount($this->session, false),
            'previous' => $this->settlementService->getBillCount($this->session, true),
        ];

        $html = $this->view()->render('pages.meeting.fee.page')
            ->with('session', $this->session)
            ->with('fees', $fees)
            ->with('settlements', $settlements)
            ->with('bills', $bills)
            ->with('zero', $this->settlementService->getFormattedAmount(0))
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $feeCount));
        if($this->session->closed)
        {
            $html->with('summary', $this->meetingService->getFeesSummary($this->session));
        }
        $this->response->html('meeting-fees-page', $html);

        $feeId = jq()->parent()->attr('data-fee-id')->toInt();
        $this->jq('.btn-fee-settlements')->click($this->cl(Settlement::class)->rq()->home($feeId));

        return $this->response;
    }
}
