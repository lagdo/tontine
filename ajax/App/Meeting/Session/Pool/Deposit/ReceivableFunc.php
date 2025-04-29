<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\Session\FuncComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;

/**
 * @before getPool
 */
class ReceivableFunc extends FuncComponent
{
    use PoolTrait;
    use DepositTrait;

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function addDeposit(int $receivableId)
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $this->depositService->createDeposit($pool, $session, $receivableId);

        $this->showTotal();
        $this->cl(ReceivablePage::class)->page();
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function delDeposit(int $receivableId)
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $this->depositService->deleteDeposit($pool, $session, $receivableId);

        $this->showTotal();
        $this->cl(ReceivablePage::class)->page();
    }

    /**
     * @return mixed
     */
    public function addAllDeposits()
    {
        $pool = $this->stash()->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->depositService->createAllDeposits($pool, $session);

        $this->showTotal();
        $this->cl(ReceivablePage::class)->page();
    }

    /**
     * @return mixed
     */
    public function delAllDeposits()
    {
        $pool = $this->stash()->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->depositService->deleteAllDeposits($pool, $session);

        $this->showTotal();
        $this->cl(ReceivablePage::class)->page();
    }
}
