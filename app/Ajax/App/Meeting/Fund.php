<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Fund extends CallableClass
{
    /**
     * @di
     * @var MeetingService
     */
    protected MeetingService $meetingService;

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
    public function show($session, $meetingService)
    {
        $this->session = $session;
        $this->meetingService = $meetingService;

        $this->deposits();
        $this->remittances();

        return $this->response;
    }

    public function deposits()
    {
        $tontine = $this->meetingService->getTontine();
        $html = $this->view()->render('pages.meeting.fund.deposits')
            ->with('tontine', $tontine)->with('session', $this->session)
            ->with('funds', $this->meetingService->getFundsWithReceivables($this->session));
        if($this->session->closed)
        {
            $html->with('summary', $this->meetingService->getFundsSummary($this->session));
        }
        $this->response->html('meeting-deposits', $html);

        $this->jq('#btn-deposits-refresh')->click($this->rq()->deposits());
        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-fund-deposits')->click($this->cl(Deposit::class)->rq()->home($fundId));

        return $this->response;
    }

    public function remittances()
    {
        $tontine = $this->meetingService->getTontine();
        $html = $this->view()->render('pages.meeting.fund.remittances')
            ->with('tontine', $tontine)->with('session', $this->session)
            ->with('funds', $this->meetingService->getFundsWithPayables($this->session));
        if($this->session->closed)
        {
            $html->with('summary', $this->meetingService->getFundsSummary($this->session));
        }
        $this->response->html('meeting-remittances', $html);

        $this->jq('#btn-remittances-refresh')->click($this->rq()->remittances());
        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $remittanceClass = ($tontine->is_mutual ? Mutual\Remittance::class : Financial\Remittance::class);
        $this->jq('.btn-fund-remittances')->click($this->cl($remittanceClass)->rq()->home($fundId));

        return $this->response;
    }
}
