<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Ajax\App\Meeting\Session\Charge\Settlement\Total;

class SettlementFunc extends FuncComponent
{
    private function showTotal()
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->stash()->set('meeting.session.settlement.count', $settlement->total ?? 0);
        $this->stash()->set('meeting.session.settlement.amount', $settlement->amount ?? 0);
        $this->stash()->set('meeting.session.bill.count',
            $this->billService->getBillCount($charge, $session));

        $this->cl(Total::class)->item('libre')->render();
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.libre.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.libre.filter', $onlyUnpaid);

        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     * @param int $billId
     *
     * @return mixed
     */
    public function addSettlement(int $billId)
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->createSettlement($charge, $session, $billId);

        $this->showTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     * @param int $billId
     *
     * @return mixed
     */
    public function delSettlement(int $billId)
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->deleteSettlement($charge, $session, $billId);

        $this->showTotal();
        $this->cl(SettlementPage::class)->page();
    }
}
