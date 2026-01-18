<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

#[Export(base: ['render'], except: ['show'])]
class Deposit extends Component
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
        $session = $this->stash()->get('meeting.session');
        return $this->renderTpl('pages.meeting.session.deposit.home', [
            'session' => $session,
            'pools' => $this->poolService->getPoolsWithReceivables($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-meeting-deposits');
    }

    public function show(): void
    {
        $this->render();
    }
}
