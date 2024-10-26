<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Settlement;

use App\Ajax\Cache;
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
        return (string)$this->renderView('pages.meeting.charge.settlement.total', [
            'billCount' => Cache::get('meeting.session.bill.count'),
            'settlementCount' => Cache::get('meeting.session.settlement.count'),
            'settlementAmount' => Cache::get('meeting.session.settlement.amount'),
        ]);
    }
}
