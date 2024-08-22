<?php

namespace App\Ajax\Web\Meeting\Session\Pool;

use App\Ajax\OpenedSessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;

class Deposit extends OpenedSessionCallable
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
        $html = $this->renderView('pages.meeting.deposit.home', [
            'session' => $this->session,
            'pools' => $this->poolService->getPoolsWithReceivables($this->session),
        ]);
        $this->response->html('meeting-deposits', $html);
        $this->response->call('makeTableResponsive', 'meeting-deposits');

        $this->jq('#btn-deposits-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-deposits')
            ->click($this->rq(Deposit\Pool::class)->home($poolId));

        return $this->response;
    }
}
