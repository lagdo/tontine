<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;

#[Exclude]
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
    public function html(): string
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');

        return $this->renderTpl('pages.meeting.session.remitment.payable.page', [
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
        $this->response->jo('tontine')->makeTableResponsive('content-session-pool-remitments');
    }
}
