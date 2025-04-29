<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Charge\Component;
use Ajax\App\Meeting\Session\Charge\Settlement\Action;
use Ajax\App\Meeting\Session\Charge\Settlement\Total;
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
        return $this->renderView('pages.meeting.session.charge.fixed.settlement.home', [
            'charge' => $this->stash()->get('meeting.session.charge'),
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
        $this->bag('meeting')->set('settlement.fixed.filter', null);
        $this->bag('meeting')->set('settlement.fixed.search', '');
        $this->bag('meeting')->set('settlement.fixed.page', 1);

        $this->render();
    }

    private function showTotal()
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->stash()->set('meeting.session.settlement.count', $settlement->total ?? 0);
        $this->stash()->set('meeting.session.settlement.amount', $settlement->amount ?? 0);
        $this->stash()->set('meeting.session.bill.count',
            $this->billService->getBillCount($charge, $session));

        $this->cl(Action::class)->item('fixed')->render();
        $this->cl(Total::class)->item('fixed')->render();
    }
}
