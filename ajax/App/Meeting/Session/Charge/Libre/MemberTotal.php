<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Stringable;

#[Exclude]
class MemberTotal extends Component
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
        return $this->renderView('pages.meeting.session.charge.libre.member.total', [
            'settlementCount' => $this->stash()->get('meeting.session.settlement.count'),
            'settlementAmount' => $this->stash()->get('meeting.session.settlement.amount'),
        ]);
    }
}
