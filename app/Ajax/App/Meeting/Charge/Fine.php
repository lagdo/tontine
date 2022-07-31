<?php

namespace App\Ajax\App\Meeting\Charge;

use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\FineSettlementService;
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
     * @var MeetingService
     */
    protected MeetingService $meetingService;

    /**
     * @di
     * @var FineSettlementService
     */
    protected FineSettlementService $settlementService;

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
        $html = $this->view()->render('pages.meeting.fine.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fines', $html);
        $this->jq('#btn-fines-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $fines = $this->meetingService->getFines($this->session, $pageNumber);
        $fineCount = $this->meetingService->getFineCount();
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

        $html = $this->view()->render('pages.meeting.fine.page')
            ->with('session', $this->session)
            ->with('fines', $fines)
            ->with('settlements', $settlements)
            ->with('bills', $bills)
            ->with('zero', $this->settlementService->getFormattedAmount(0))
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $fineCount));
        if($this->session->closed)
        {
            $html->with('summary', $this->meetingService->getFinesSummary($this->session));
        }
        $this->response->html('meeting-fines-page', $html);

        $fineId = jq()->parent()->attr('data-fine-id')->toInt();
        $this->jq('.btn-fine-add')->click($this->cl(Member::class)->rq()->home($fineId));
        $this->jq('.btn-fine-settlements')->click($this->cl(Settlement::class)->rq()->home($fineId));

        return $this->response;
    }
}
