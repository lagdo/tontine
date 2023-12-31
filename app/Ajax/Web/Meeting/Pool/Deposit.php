<?php

namespace App\Ajax\Web\Meeting\Pool;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\SessionService;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Deposit extends CallableClass
{
    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * The constructor
     *
     * @param SessionService $sessionService
     * @param PoolService $poolService
     */
    public function __construct(protected SessionService $sessionService,
        protected PoolService $poolService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
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
        $html = $this->render('pages.meeting.deposit.home', [
            'session' => $this->session,
            'pools' => $this->poolService->getPoolsWithReceivables($this->session),
        ]);
        $this->response->html('meeting-deposits', $html);

        $this->jq('#btn-deposits-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-deposits')
            ->click($this->cl(Deposit\Pool::class)->rq()->home($poolId));

        return $this->response;
    }
}
