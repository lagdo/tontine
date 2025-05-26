<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Component;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Stringable;

/**
 * @exclude
 */
class Total extends Component
{
    /**
     * The constructor
     *
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private BalanceCalculator $balanceCalculator)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $pool = $this->stash()->get('meeting.pool');

        return $this->renderView('pages.meeting.session.deposit.receivable.total', [
            'depositCount' => $this->stash()->get('meeting.pool.deposit.count'),
            'depositAmount' => $this->balanceCalculator->getPoolDepositAmount($pool, $session),
        ]);
    }
}
