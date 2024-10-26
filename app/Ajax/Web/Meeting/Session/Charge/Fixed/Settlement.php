<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Fixed;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\Session\Charge\ChargeComponent;
use App\Ajax\Web\Meeting\Session\Charge\Settlement\Action;
use App\Ajax\Web\Meeting\Session\Charge\Settlement\Total;

use function trim;

class Settlement extends ChargeComponent
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.charge.fixed.settlement.home', [
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
        $this->bag('meeting')->set('settlement.fixed.filter', null);
        $this->bag('meeting')->set('settlement.fixed.search', '');
        $this->bag('meeting')->set('settlement.fixed.page', 1);

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

        $this->cl(Action::class)->render();
        $this->cl(Total::class)->render();
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.fixed.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.fixed.filter', $onlyUnpaid);

        return $this->cl(SettlementPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('settlement.fixed.search', trim($search));

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

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function addAllSettlements()
    {
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');
        $this->settlementService->createAllSettlements($charge, $session);

        $this->showTotal();

        return $this->cl(SettlementPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function delAllSettlements()
    {
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');
        $this->settlementService->deleteAllSettlements($charge, $session);

        $this->showTotal();

        return $this->cl(SettlementPage::class)->page();
    }
}
