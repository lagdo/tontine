<?php

namespace App\Ajax\Web\Meeting\Summary\Pool;

use App\Ajax\SessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;

/**
 * @exclude
 */
class Remitment extends SessionCallable
{
    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    public function show(SessionModel $session)
    {
        $this->session = $session;

        $hasAuctions = $this->poolService->hasPoolWithAuction();
        $html = $this->renderView('pages.meeting.summary.remitment.home', [
            'session' => $this->session,
            'pools' => $this->poolService->getPoolsWithPayables($this->session),
            'hasAuctions' => $hasAuctions,
        ]);
        $this->response->html('meeting-remitments', $html);
        $this->response->js()->makeTableResponsive('meeting-remitments');

        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->response->jq('.btn-pool-remitments')->click($this->rq()->remitments($poolId));

        return $this->response;
    }
}
