<?php

namespace Ajax\App\Meeting\Summary\Pool;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Stringable;

/**
 * @exclude
 */
class Deposit extends Component
{
    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.deposit.home', [
            'session' => $session,
            'pools' => $this->poolService->getPoolsWithReceivables($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-summary-deposits');
    }
}
