<?php

namespace App\Ajax\Web\Meeting\Pool;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;

class Deposit extends CallableSessionClass
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
