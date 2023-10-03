<?php

namespace App\Ajax\Web\Meeting\Pool;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Deposit extends CallableClass
{
    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

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
    protected function getSession()
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
        $html = $this->view()->render('tontine.pages.meeting.deposit.home')
            ->with('session', $this->session)
            ->with('pools', $this->poolService->getPoolsWithReceivables($this->session));
        $this->response->html('meeting-deposits', $html);

        $this->jq('#btn-deposits-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-deposits')
            ->click($this->cl(Deposit\Pool::class)->rq()->home($poolId));

        return $this->response;
    }
}
