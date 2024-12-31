<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Stringable;

class Deposit extends MeetingComponent
{
    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');

        return $this->renderView('pages.meeting.deposit.home', [
            'session' => $session,
            'pools' => $this->poolService->getPoolsWithReceivables($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-deposits');
    }
}
