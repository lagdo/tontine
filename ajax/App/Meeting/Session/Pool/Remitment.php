<?php

namespace Ajax\App\Meeting\Session\Pool;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

class Remitment extends MeetingComponent
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
        $session = $this->cache->get('meeting.session');

        return $this->renderView('pages.meeting.remitment.home', [
            'session' => $session,
            'pools' => $this->poolService->getPoolsWithPayables($session),
            'hasAuctions' => $this->poolService->hasPoolWithAuction(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-remitments');
    }
}