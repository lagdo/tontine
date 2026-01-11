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
        $fund = $this->settlementService->getFund($this->round(), $fundId);
        if(!$fund)
        {
            throw new MessageException(trans('tontine.fund.errors.not_found'));
        }

        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $cancelled = $this->settlementService->createSettlement($charge,
            $session, $billId, $fund);
        if($cancelled > 0)
        {
            $this->alert()->warning(trans('meeting.settlement.warnings.cancelled'));
        }

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
        $fund = $this->settlementService->getFund($this->round(), $fundId);
        if(!$fund)
        {
            throw new MessageException(trans('tontine.fund.errors.not_found'));
        }

        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('settlement.libre.search', '');
        $cancelled = $this->settlementService->createAllSettlements($charge,
            $session, $search, $fund);
        if($cancelled > 0)
        {
            $this->alert()->warning(trans('meeting.settlement.warnings.cancelled-some'));
        }

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
