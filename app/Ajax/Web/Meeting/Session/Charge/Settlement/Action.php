<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Settlement;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Pool\DepositService;

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
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.charge.settlement.action', [
            'session' => $this->cache->get('meeting.session'),
            'charge' => $this->cache->get('meeting.session.charge'),
            'billCount' => $this->cache->get('meeting.session.bill.count'),
            'settlementCount' => $this->cache->get('meeting.session.settlement.count'),
        ]);
    }
}
