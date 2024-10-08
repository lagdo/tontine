<?php

namespace App\Ajax\Web\Meeting\Session\Pool;

use App\Ajax\OpenedSessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;

class Remitment extends OpenedSessionCallable
{
    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

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
        $html = $this->renderView('pages.meeting.remitment.home', [
            'session' => $this->session,
            'pools' => $this->poolService->getPoolsWithPayables($this->session),
            'hasAuctions' => $hasAuctions,
        ]);
        $this->response->html('meeting-remitments', $html);
        $this->response->call('makeTableResponsive', 'meeting-remitments');

        if($hasAuctions)
        {
            $this->jq('#btn-remitment-auctions')->click($this->rq(Auction::class)->home());
        }
        $this->jq('#btn-remitments-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-remitments')
            ->click($this->rq(Remitment\Pool::class)->home($poolId));

        return $this->response;
    }
}
