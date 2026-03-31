<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Charge\BillService;

#[Exclude]
class SettlementAll extends Component
{
    /**
     * The constructor
     *
     * @param BillService $billService
     */
    public function __construct(private BillService $billService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('settlement.fixed.search', '');

        return $this->renderTpl('pages.meeting.session.charge.fixed.settlement.all', [
            'billCount' => $this->billService->getBillCount($charge, $session, $search),
            'settlementCount' => $this->billService->getBillCount($charge, $session, $search, true),
        ]);
    }
}
