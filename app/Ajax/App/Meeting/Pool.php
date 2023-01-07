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
class Pool extends CallableClass
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
        $html = $this->view()->render('pages.meeting.pool.deposits')
            ->with('tontine', $tontine)->with('session', $this->session)
            ->with('pools', $this->meetingService->getPoolsWithReceivables($this->session));
        if($this->session->closed)
        {
            $html->with('summary', $this->meetingService->getPoolsSummary($this->session));
        }
        $this->response->html('meeting-deposits', $html);

        $this->jq('#btn-deposits-refresh')->click($this->rq()->deposits());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-deposits')->click($this->cl(Deposit::class)->rq()->home($poolId));

        return $this->response;
    }

    public function remittances()
    {
        $tontine = $this->meetingService->getTontine();
        $html = $this->view()->render('pages.meeting.pool.remittances')
            ->with('tontine', $tontine)->with('session', $this->session)
            ->with('pools', $this->meetingService->getPoolsWithPayables($this->session));
        if($this->session->closed)
        {
            $html->with('summary', $this->meetingService->getPoolsSummary($this->session));
        }
        $this->response->html('meeting-remittances', $html);

        $this->jq('#btn-remittances-refresh')->click($this->rq()->remittances());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $remittanceClass = ($tontine->is_mutual ? Mutual\Remittance::class : Financial\Remittance::class);
        $this->jq('.btn-pool-remittances')->click($this->cl($remittanceClass)->rq()->home($poolId));

        return $this->response;
    }
}
