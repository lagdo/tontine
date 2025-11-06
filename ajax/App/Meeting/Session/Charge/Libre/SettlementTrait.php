<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

trait SettlementTrait
{
    protected function setSettlement(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->stash()->set('meeting.session.settlement.count', $settlement->total);
        $this->stash()->set('meeting.session.settlement.amount', $settlement->amount);
        $this->stash()->set('meeting.session.bill.count',
            $this->billService->getBillCount($charge, $session));
    }

    protected function showTotal(): void
    {
        $this->setSettlement();
        $this->cl(SettlementTotal::class)->render();
        $this->cl(SettlementAll::class)->render();
    }
}
