<?php

namespace App\Ajax\Web\Meeting\Session\Pool;

use App\Ajax\Cache;
use App\Ajax\MeetingComponent;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

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
    public function html(): string
    {
        $session = Cache::get('meeting.session');

        return (string)$this->renderView('pages.meeting.deposit.home', [
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
