<?php

namespace App\Ajax\Web\Meeting\Session\Pool\Deposit;

use App\Ajax\Component;
use Siak\Tontine\Service\BalanceCalculator;

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
    public function html(): string
    {
        $session = $this->cache->get('meeting.session');
        $pool = $this->cache->get('meeting.pool');

        return (string)$this->renderView('pages.meeting.deposit.pool.total', [
            'depositCount' => $this->cache->get('meeting.pool.deposit.count'),
            'depositAmount' => $this->balanceCalculator->getPoolDepositAmount($pool, $session),
        ]);
    }
}
