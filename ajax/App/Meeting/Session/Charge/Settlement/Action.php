<?php

namespace Ajax\App\Meeting\Session\Charge\Settlement;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Stringable;

/**
 * @exclude
 */
class Action extends Component
{
    /**
     * The constructor
     *
     * @param DepositService $depositService
     */
    public function __construct(protected DepositService $depositService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.charge.settlement.action', [
            'session' => $this->stash()->get('meeting.session'),
            'charge' => $this->stash()->get('meeting.session.charge'),
            'billCount' => $this->stash()->get('meeting.session.bill.count'),
            'settlementCount' => $this->stash()->get('meeting.session.settlement.count'),
        ]);
    }
}
