<?php

namespace Ajax\App\Meeting\Summary\Charge\Fixed;

use Ajax\App\Meeting\Summary\Charge\Component;
use Ajax\App\Meeting\Summary\Charge\Settlement\Total;
use Stringable;

class Settlement extends Component
{
    /**
     * @var string
     */
    protected $overrides = Fee::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.charge.fixed.settlement.home', [
            'charge' => $this->stash()->get('summary.session.charge'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SettlementPage::class)->page();
        $this->showTotal();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId)
    {
        $this->bag('summary')->set('settlement.fixed.filter', null);
        $this->bag('summary')->set('settlement.fixed.search', '');
        $this->bag('summary')->set('settlement.fixed.page', 1);

        $this->render();
    }

    private function showTotal()
    {
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->stash()->set('summary.session.settlement.count', $settlement->total ?? 0);
        $this->stash()->set('summary.session.settlement.amount', $settlement->amount ?? 0);
        $this->stash()->set('summary.session.bill.count',
            $this->billService->getBillCount($charge, $session));

        $this->cl(Total::class)->item('fixed')->render();
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('summary')->get('settlement.fixed.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('summary')->set('settlement.fixed.filter', $onlyUnpaid);
        $this->bag('summary')->set('settlement.fixed.page', 1);

        $this->cl(SettlementPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('summary')->set('settlement.fixed.search', trim($search));
        $this->bag('summary')->set('settlement.fixed.page', 1);

        $this->cl(SettlementPage::class)->page();
    }
}
