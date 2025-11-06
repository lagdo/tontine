<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Stringable;

#[Exclude]
class SettlementTotal extends Component
{
    /**
     * The constructor
     *
     * @param BillService $billService
     * @param SettlementService $settlementService
     */
    public function __construct(private BillService $billService,
        private SettlementService $settlementService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        [$count, $amount] = $this->settlementService->getSettlementTotal($charge, $session);

        return $this->renderView('pages.meeting.session.charge.fixed.settlement.total', [
            'billCount' => $this->billService->getBillCount($charge, $session),
            'settlementCount' => $count,
            'settlementAmount' => $amount,
        ]);
    }
}
