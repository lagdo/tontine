<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\Session\Charge\ChargeComponent;
use App\Ajax\Web\Meeting\Session\Charge\Settlement\Total;

class Settlement extends ChargeComponent
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.charge.libre.settlement.home', [
            'charge' => Cache::get('meeting.session.charge'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function after()
    {
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId)
    {
        $this->bag('meeting')->set('charge.id', $chargeId);
        $this->bag('meeting')->set('settlement.libre.search', '');
        $this->bag('meeting')->set('settlement.libre.filter', null);
        $this->bag('meeting')->set('settlement.libre.page', 1);

        return $this->render();
    }

    private function showTotal()
    {
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        Cache::set('meeting.session.settlement.count', $settlement->total ?? 0);
        Cache::set('meeting.session.settlement.amount', $settlement->amount ?? 0);
        Cache::set('meeting.session.bill.count',
            $this->billService->getBillCount($charge, $session));

        $this->cl(Total::class)->item('libre')->render();
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.libre.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.libre.filter', $onlyUnpaid);

        return $this->cl(SettlementPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     * @param int $billId
     *
     * @return mixed
     */
    public function addSettlement(int $billId)
    {
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');
        $this->settlementService->createSettlement($charge, $session, $billId);

        $this->showTotal();

        return $this->cl(SettlementPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     * @param int $billId
     *
     * @return mixed
     */
    public function delSettlement(int $billId)
    {
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');
        $this->settlementService->deleteSettlement($charge, $session, $billId);

        $this->showTotal();

        return $this->cl(SettlementPage::class)->page();
    }
}
