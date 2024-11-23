<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Component;
use Siak\Tontine\Service\BalanceCalculator;
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
        $session = $this->cache->get('meeting.session');
        $pool = $this->cache->get('meeting.pool');

        return $this->renderView('pages.meeting.deposit.pool.total', [
            'depositCount' => $this->cache->get('meeting.pool.deposit.count'),
            'depositAmount' => $this->balanceCalculator->getPoolDepositAmount($pool, $session),
        ]);
    }
}
