<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Charge\BillService;

#[Exclude]
class SavingAll extends Component
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
        $search = $this->bag('meeting')->get('settlement.libre.search', '');

        return $this->renderTpl('pages.meeting.session.charge.libre.saving.all', [
            'billCount' => $this->billService->getBillCount($charge, $session, $search),
            'settlementCount' => $this->billService->getBillCount($charge, $session, $search, true),
        ]);
    }
}
