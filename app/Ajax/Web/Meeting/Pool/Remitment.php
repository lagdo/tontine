<?php

namespace App\Ajax\Web\Meeting\Pool;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
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
        $hasAuctions = $this->poolService->hasPoolWithAuction();
        $html = $this->render('pages.meeting.remitment.home', [
            'session' => $this->session,
            'pools' => $this->poolService->getPoolsWithPayables($this->session),
            'hasAuctions' => $hasAuctions,
        ]);
        $this->response->html('meeting-remitments', $html);

        if($hasAuctions)
        {
            $this->jq('#btn-remitment-auctions')->click($this->cl(Auction::class)->rq()->home());
        }
        $this->jq('#btn-remitments-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-remitments')
            ->click($this->cl(Remitment\Pool::class)->rq()->home($poolId));

        return $this->response;
    }
}
