<?php

namespace Ajax\App\Meeting\Summary\Charge\Settlement;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Stringable;

#[Exclude]
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
        return $this->renderView('pages.meeting.summary.charge.settlement.total', [
            'billCount' => $this->stash()->get('summary.session.bill.count'),
            'settlementCount' => $this->stash()->get('summary.session.settlement.count'),
            'settlementAmount' => $this->stash()->get('summary.session.settlement.amount'),
        ]);
    }
}
