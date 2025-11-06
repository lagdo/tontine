<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('checkChargeEdit')]
class SettlementFunc extends FuncComponent
{
    use SettlementTrait;
    use ChargeTrait;

    /**
     * @param int $billId
     *
     * @return mixed
     */
    public function addSettlement(int $billId): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->createSettlement($charge, $session, $billId);

        $this->showTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @param int $billId
     *
     * @return mixed
     */
    public function delSettlement(int $billId): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->deleteSettlement($charge, $session, $billId);

        $this->showTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @return mixed
     */
    public function addSettlements(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->createAllSettlements($charge, $session);

        $this->showTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @return mixed
     */
    public function delSettlements(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->deleteAllSettlements($charge, $session);

        $this->showTotal();
        $this->cl(SettlementPage::class)->page();
    }
}
