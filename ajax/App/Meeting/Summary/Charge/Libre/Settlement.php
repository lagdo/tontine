<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

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
        return $this->renderView('pages.meeting.summary.charge.libre.settlement.home', [
            'charge' => $this->stash()->get('summary.session.charge'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SettlementPage::class)->page();
        $this->showTotal();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId): void
    {
        $this->bag('summary')->set('settlement.libre.search', '');
        $this->bag('summary')->set('settlement.libre.filter', null);
        $this->bag('summary')->set('settlement.libre.page', 1);

        $this->render();
    }

    private function showTotal(): void
    {
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->stash()->set('summary.session.settlement.count', $settlement->total ?? 0);
        $this->stash()->set('summary.session.settlement.amount', $settlement->amount ?? 0);
        $this->stash()->set('summary.session.bill.count',
            $this->billService->getBillCount($charge, $session));

        $this->cl(Total::class)->item('libre')->render();
    }

    public function toggleFilter(): void
    {
        $onlyUnpaid = $this->bag('summary')->get('settlement.libre.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('summary')->set('settlement.libre.filter', $onlyUnpaid);
        $this->bag('summary')->set('settlement.libre.page', 1);

        $this->cl(SettlementPage::class)->page();
    }
}
