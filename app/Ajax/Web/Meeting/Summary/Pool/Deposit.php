<?php

namespace App\Ajax\Web\Meeting\Summary\Pool;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;

/**
 * @exclude
 */
class Deposit extends CallableSessionClass
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

        $html = $this->renderView('pages.meeting.summary.deposit.home', [
            'session' => $this->session,
            'pools' => $this->poolService->getPoolsWithReceivables($this->session),
        ]);
        $this->response->html('meeting-deposits', $html);
        $this->response->call('makeTableResponsive', 'meeting-deposits');

        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-deposits')->click($this->rq()->deposits($poolId));

        return $this->response;
    }
}
