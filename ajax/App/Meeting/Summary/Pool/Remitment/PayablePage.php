<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

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
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.remitment.payable.page', [
            'pool' => $pool,
            'session' => $session,
            'payables' => $this->remitmentService->getPayables($pool, $session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-pool-remitments');
    }
}
