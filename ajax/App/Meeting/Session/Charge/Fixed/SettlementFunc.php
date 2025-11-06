<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Exclude;

class SettlementFunc extends FuncComponent
{
    use ChargeTrait;

    #[Exclude]
    public function showTotal(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->stash()->set('meeting.session.settlement.count', $settlement->total ?? 0);
        $this->stash()->set('meeting.session.settlement.amount', $settlement->amount ?? 0);
        $this->stash()->set('meeting.session.bill.count',
            $this->billService->getBillCount($charge, $session));

        $this->cl(SettlementAll::class)->render();
        $this->cl(SettlementTotal::class)->render();
    }

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

        $this->showTotal();
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

        $this->showTotal();
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
        $this->settlementService->createAllSettlements($charge, $session);

        $this->showTotal();
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
        $this->settlementService->deleteAllSettlements($charge, $session);

        $this->showTotal();
        $this->cl(SettlementPage::class)->page();
    }
}
