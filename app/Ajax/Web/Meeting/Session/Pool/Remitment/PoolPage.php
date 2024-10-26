<?php

namespace App\Ajax\Web\Meeting\Session\Pool\Remitment;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;

/**
 * @exclude
 */
class PoolPage extends Component
{
    /**
     * The constructor
     *
     * @param RemitmentService $remitmentService
     */
    public function __construct(protected RemitmentService $remitmentService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = Cache::get('meeting.pool');
        $session = Cache::get('meeting.session');

        return (string)$this->renderView('pages.meeting.remitment.pool.page', [
            'pool' => $pool,
            'session' => $session,
            'payables' => $this->remitmentService->getPayables($pool, $session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-pool-remitments');
    }
}
