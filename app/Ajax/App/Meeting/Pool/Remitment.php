<?php

namespace App\Ajax\App\Meeting\Pool;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getPool
 */
class Remitment extends CallableClass
{
    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    /**
     * @return void
     */
    protected function getPool()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->poolService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.remitment.home')
            ->with('session', $this->session)
            ->with('pools', $this->poolService->getPoolsWithPayables($this->session));
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-remitments')->click($this->rq()->pool($poolId));

        return $this->response;
    }

    public function pool(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        if(!$pool || $this->session->disabled($pool))
        {
            $this->notify->error(trans('tontine.session.errors.disabled'), trans('common.titles.error'));
            return $this->response;
        }

        $this->bag('meeting')->set('remitment.pool.id', $poolId);

        $remitmentClass = $pool->remit_payable ?
            Remitment\Payable::class : Remitment\Subscription::class;

        return $this->cl($remitmentClass)->show($this->session, $pool);
    }
}
