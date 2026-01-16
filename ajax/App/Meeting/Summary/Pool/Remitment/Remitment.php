<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

#[Export(base: ['render'], except: ['show'])]
class Remitment extends Component
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
        $session = $this->stash()->get('summary.session');

        return $this->renderTpl('pages.meeting.summary.remitment.home', [
            'session' => $session,
            'pools' => $this->poolService->getPoolsWithPayables($session),
            'hasAuctions' => $this->poolService->hasPoolWithAuction($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-session-remitments');
    }

    public function show(): void
    {
        $this->render();
    }
}
