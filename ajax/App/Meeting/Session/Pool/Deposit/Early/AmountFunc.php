<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Early;

use Ajax\App\Meeting\Session\Pool\Deposit\AmountBase;
use Siak\Tontine\Model\Receivable as ReceivableModel;

/**
 * @before getNextSession
 */
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
        $nextSession = $this->stash()->get('meeting.early.session');
        return $this->depositService->getReceivable($pool,
            $session, $nextSession, $receivableId);
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
        $nextSession = $this->stash()->get('meeting.early.session');
        if($amount <= 0)
        {
            $this->depositService->deleteDeposit($pool,
                $session, $nextSession, $receivableId);
            return;
        }

        $this->depositService->createDeposit($pool,
            $session, $nextSession, $receivableId, $amount);
    }
}
