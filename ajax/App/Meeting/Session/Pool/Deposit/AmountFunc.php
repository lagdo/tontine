<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Siak\Tontine\Model\Receivable as ReceivableModel;

class AmountFunc extends AmountBase
{
    use DepositTrait;

    /**
     * @param int $receivableId
     *
     * @return ReceivableModel|null
     */
    protected function getReceivable(int $receivableId): ?ReceivableModel
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        return $this->depositService->getReceivable($pool, $session, $receivableId);
    }

    /**
     * @param int $receivableId
     * @param int $amount
     *
     * @return void
     */
    protected function saveDeposit(int $receivableId, int $amount): void
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        if($amount <= 0)
        {
            $this->depositService->deleteDeposit($pool, $session, $receivableId);
            return;
        }

        $this->depositService->createDeposit($pool, $session, $receivableId, $amount);
    }
}
