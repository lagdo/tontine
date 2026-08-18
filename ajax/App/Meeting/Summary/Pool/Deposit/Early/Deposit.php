<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Early;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\Deposit\Deposit as SessionDeposit;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

#[Export(base: ['render'])]
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
     * @return string
     */
    protected function overrides(): string
    {
        return SessionDeposit::class;
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderTpl('pages.meeting.summary.deposit.early.home', [
            'session' => $session,
            'sessions' => $this->poolService->getNextSessions($session),
            'pools' => $this->poolService->getPoolsWithEarlyDeposits($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-meeting-deposits');
    }
}
