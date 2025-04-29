<?php

namespace Ajax\App\Meeting\Session\Charge\Settlement;

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
        return $this->renderView('pages.meeting.session.charge.settlement.total', [
            'billCount' => $this->stash()->get('meeting.session.bill.count'),
            'settlementCount' => $this->stash()->get('meeting.session.settlement.count'),
            'settlementAmount' => $this->stash()->get('meeting.session.settlement.amount'),
        ]);
    }
}
