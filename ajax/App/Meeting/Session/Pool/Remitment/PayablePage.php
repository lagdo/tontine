<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Stringable;

/**
 * @exclude
 */
class PayablePage extends Component
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
    public function html(): Stringable
    {
        $pool = $this->cache()->get('meeting.pool');
        $session = $this->cache()->get('meeting.session');

        return $this->renderView('pages.meeting.remitment.payable.page', [
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
