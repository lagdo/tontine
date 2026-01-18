<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Late;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\Deposit\Deposit as SessionDeposit;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

#[Export(base: ['render'])]
class Deposit extends Component
{
    /**
     * @var string
     */
    protected $overrides = SessionDeposit::class;

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
        return $this->renderTpl('pages.meeting.summary.deposit.late.home', [
            'session' => $session,
            'pools' => $this->poolService->getPoolsWithLateDeposits($session),
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
