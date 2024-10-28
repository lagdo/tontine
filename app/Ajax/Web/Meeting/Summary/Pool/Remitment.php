<?php

namespace App\Ajax\Web\Meeting\Summary\Pool;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

/**
 * @exclude
 */
class Remitment extends Component
{
    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    public function html(): string
    {
        $session = $this->cache->get('summary.session');

        return (string)$this->renderView('pages.meeting.summary.remitment.home', [
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
