<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
class SettlementAll extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.charge.libre.settlement.all', [
            'settlementCount' => $this->stash()->get('meeting.session.settlement.count'),
            'billCount' => $this->stash()->get('meeting.session.bill.count'),
        ]);
    }
}
