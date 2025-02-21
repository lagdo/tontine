<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Siak\Tontine\Service\Meeting\Pool\DepositService;

trait DepositTrait
{
    /**
     * @di
     * @var DepositService
     */
    protected DepositService $depositService;

    private function showTotal()
    {
        $session = $this->stash()->get('meeting.session');
        $pool = $this->stash()->get('meeting.pool');
        $this->stash()->set('meeting.pool.deposit.count',
            $this->depositService->countDeposits($pool, $session));

        $this->cl(Total::class)->render();
        $this->cl(Action::class)->render();
    }
}
