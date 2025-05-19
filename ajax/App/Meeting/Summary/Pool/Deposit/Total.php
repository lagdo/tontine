<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit;

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
        $session = $this->stash()->get('summary.session');
        $pool = $this->stash()->get('summary.pool');

        return $this->renderView('pages.meeting.summary.deposit.receivable.total', [
            'depositCount' => $this->stash()->get('summary.pool.deposit.count'),
            'depositAmount' => $this->balanceCalculator->getPoolDepositAmount($pool, $session),
        ]);
    }
}
