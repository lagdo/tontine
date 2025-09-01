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
    public function addDeposit(int $receivableId): void
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
    public function delDeposit(int $receivableId): void
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
    public function addAllDeposits(): void
    {
        $pool = $this->stash()->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $search = $this->bag('meeting')->get('receivable.search', '');
        $this->depositService->createAllDeposits($pool, $session, $search);

        $this->showTotal();
        $this->cl(ReceivablePage::class)->page();
    }

    /**
     * @return mixed
     */
    public function delAllDeposits(): void
    {
        $pool = $this->stash()->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $search = $this->bag('meeting')->get('receivable.search', '');
        $this->depositService->deleteAllDeposits($pool, $session, $search);

        $this->showTotal();
        $this->cl(ReceivablePage::class)->page();
    }
}
