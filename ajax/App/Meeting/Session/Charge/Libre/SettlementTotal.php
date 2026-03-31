<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;

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
    public function html(): string
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        [$count, $amount] = $this->settlementService->getSettlementTotal($charge, $session);

        return $this->renderTpl('pages.meeting.session.charge.libre.settlement.total', [
            'billCount' => $this->billService->getBillCount($charge, $session),
            'settlementCount' => $count,
            'settlementAmount' => $amount,
        ]);
    }
}
