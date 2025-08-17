<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Late;

use Ajax\App\Meeting\Session\Pool\Deposit\AmountBase;
use Siak\Tontine\Model\Receivable as ReceivableModel;

class AmountFunc extends AmountBase
{
    use DepositTrait;

    /**
     * @var string
     */
    protected string $amountClass = Amount::class;

    /**
     * @var string
     */
    protected string $receivablePageClass = ReceivablePage::class;

    /**
     * @param int $receivableId
     *
     * @return ReceivableModel|null
     */
    protected function getReceivable(int $receivableId): ?ReceivableModel
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        return $this->depositService->getLateReceivable($pool, $session, $receivableId);
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
            $this->depositService->deleteLateDeposit($pool, $session, $receivableId);
            return;
        }

        $this->depositService->createLateDeposit($pool, $session, $receivableId, $amount);
    }
}
