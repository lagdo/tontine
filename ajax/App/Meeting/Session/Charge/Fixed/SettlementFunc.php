<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;

class SettlementFunc extends FuncComponent
{
    use ChargeTrait;

    /**
     * @param int $billId
     *
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function addSettlement(int $billId): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->createSettlement($charge, $session, $billId);

        $this->showSettlementTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @param int $billId
     *
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function delSettlement(int $billId): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->deleteSettlement($charge, $session, $billId);

        $this->showSettlementTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function addSettlements(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('settlement.fixed.search', '');
        $this->settlementService->createAllSettlements($charge, $session, $search);

        $this->showSettlementTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function delSettlements(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('settlement.fixed.search', '');
        $this->settlementService->deleteAllSettlements($charge, $session, $search);

        $this->showSettlementTotal();
        $this->cl(SettlementPage::class)->page();
    }
}
