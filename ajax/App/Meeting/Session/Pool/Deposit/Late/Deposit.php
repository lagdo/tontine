<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Late;

use Ajax\App\Meeting\Session\Component;
use Ajax\App\Meeting\Session\Pool\Deposit\Deposit as SessionDeposit;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Stringable;

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
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        return $this->renderView('pages.meeting.session.deposit.late.home', [
            'session' => $session,
            'pools' => $this->poolService->getPoolsWithLateDeposits($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-meeting-deposits');
    }
}
