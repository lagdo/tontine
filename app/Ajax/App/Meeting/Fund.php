<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function jq;

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

        return $this->home();
    }

    public function home()
    {
        $tontine = $this->meetingService->getTontine();
        $html = $this->view()->render('pages.meeting.fund.home')
            ->with('tontine', $tontine)->with('session', $this->session)
            ->with('funds', $this->meetingService->getFunds($this->session));
        if($this->session->closed)
        {
            $html->with('summary', $this->meetingService->getFundsSummary($this->session));
        }
        $this->response->html('meeting-funds', $html);

        $this->jq('#btn-funds-refresh')->click($this->rq()->home());
        $fundId = jq()->parent()->attr('data-fund-id');
        $this->jq('.btn-fund-deposits')->click($this->cl(Deposit::class)->rq()->home($fundId));
        if($tontine->is_mutual)
        {
            $this->jq('.btn-mutual-remittances')->click($this->cl(Mutual\Remittance::class)->rq()->home($fundId));
        }
        if($tontine->is_financial)
        {
            $this->jq('.btn-financial-remittances')->click($this->cl(Financial\Remittance::class)->rq()->home($fundId));
        }

        return $this->response;
    }
}
