<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Exception\MessageException;

use function trans;

#[Before('checkChargeEdit')]
class SavingFunc extends FuncComponent
{
    use ChargeTrait;

    /**
     * @param int $billId
     * @param int $fundId
     *
     * @return mixed
     */
    public function addSettlement(int $billId, int $fundId): void
    {
        $round = $this->stash()->get('tenant.round');
        $fund = $this->settlementService->getFund($round, $fundId);
        if(!$fund)
        {
            throw new MessageException(trans('tontine.fund.errors.not_found'));
        }

        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->createSettlement($charge, $session, $billId, $fund);

        $this->cl(SavingAll::class)->render();
        $this->cl(SavingPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function delSettlement(int $billId): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->settlementService->deleteSettlement($charge, $session, $billId);

        $this->cl(SavingAll::class)->render();
        $this->cl(SavingPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function addSettlements(int $fundId): void
    {
        $round = $this->stash()->get('tenant.round');
        $fund = $this->settlementService->getFund($round, $fundId);
        if(!$fund)
        {
            throw new MessageException(trans('tontine.fund.errors.not_found'));
        }

        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('settlement.libre.search', '');
        $this->settlementService->createAllSettlements($charge, $session, $search, $fund);

        $this->cl(SavingAll::class)->render();
        $this->cl(SavingPage::class)->page();
    }

    /**
     * @return mixed
     */
    public function delSettlements(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('settlement.libre.search', '');
        $this->settlementService->deleteAllSettlements($charge, $session, $search);

        $this->cl(SavingAll::class)->render();
        $this->cl(SavingPage::class)->page();
    }
}
