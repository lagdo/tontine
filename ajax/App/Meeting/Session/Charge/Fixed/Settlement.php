<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Charge\ChargeComponent;
use Ajax\App\Meeting\Session\Charge\FixedFee;
use Ajax\App\Meeting\Session\Charge\Settlement\Action;
use Ajax\App\Meeting\Session\Charge\Settlement\Total;
use Stringable;

use function trim;

class Settlement extends ChargeComponent
{
    /**
     * @var string
     */
    protected $overrides = FixedFee::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.charge.fixed.settlement.home', [
            'charge' => $this->cache->get('meeting.session.charge'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
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
        $this->bag('meeting')->set('settlement.fixed.filter', null);
        $this->bag('meeting')->set('settlement.fixed.search', '');
        $this->bag('meeting')->set('settlement.fixed.page', 1);

        return $this->render();
    }

    private function showTotal()
    {
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->cache->set('meeting.session.settlement.count', $settlement->total ?? 0);
        $this->cache->set('meeting.session.settlement.amount', $settlement->amount ?? 0);
        $this->cache->set('meeting.session.bill.count',
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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $this->settlementService->deleteAllSettlements($charge, $session);

        $this->showTotal();

        return $this->cl(SettlementPage::class)->page();
    }
}
