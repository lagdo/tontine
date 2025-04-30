<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit;

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
        $session = $this->stash()->get('summary.session');
        $pool = $this->stash()->get('summary.pool');
        $this->stash()->set('summary.pool.deposit.count',
            $this->depositService->countDeposits($pool, $session));

        $this->cl(Total::class)->render();
    }
}
