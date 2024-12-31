<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

trait DepositTrait
{
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
