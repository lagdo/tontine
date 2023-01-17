<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\Meeting\PoolService;
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
     * @var PoolService
     */
    protected PoolService $poolService;

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
        $this->session = $this->poolService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session, PoolService $poolService)
    {
        $this->session = $session;
        $this->poolService = $poolService;

        $this->deposits();
        $this->remitments();

        return $this->response;
    }

    public function deposits()
    {
        $tontine = $this->poolService->getTontine();
        $html = $this->view()->render('tontine.pages.meeting.pool.deposits')
            ->with('tontine', $tontine)->with('session', $this->session)
            ->with('pools', $this->poolService->getPoolsWithReceivables($this->session));
        if($this->session->closed)
        {
            $html->with('report', $this->poolService->getPoolsReport($this->session));
        }
        $this->response->html('meeting-deposits', $html);

        $this->jq('#btn-deposits-refresh')->click($this->rq()->deposits());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-deposits')->click($this->cl(Deposit::class)->rq()->home($poolId));

        return $this->response;
    }

    public function remitments()
    {
        $tontine = $this->poolService->getTontine();
        $html = $this->view()->render('tontine.pages.meeting.pool.remitments')
            ->with('tontine', $tontine)->with('session', $this->session)
            ->with('pools', $this->poolService->getPoolsWithPayables($this->session));
        if($this->session->closed)
        {
            $html->with('report', $this->poolService->getPoolsReport($this->session));
        }
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-refresh')->click($this->rq()->remitments());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $remitmentClass = ($tontine->is_mutual ? Remitment\Mutual::class : Remitment\Financial::class);
        $this->jq('.btn-pool-remitments')->click($this->cl($remitmentClass)->rq()->home($poolId));

        return $this->response;
    }
}
