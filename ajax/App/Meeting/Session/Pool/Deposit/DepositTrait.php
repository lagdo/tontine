<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

trait DepositTrait
{
    private function showTotal()
    {
        $session = $this->cache()->get('meeting.session');
        $pool = $this->cache()->get('meeting.pool');
        $this->cache()->set('meeting.pool.deposit.count',
            $this->depositService->countDeposits($pool, $session));

        $this->cl(Total::class)->render();
        $this->cl(Action::class)->render();
    }
}
