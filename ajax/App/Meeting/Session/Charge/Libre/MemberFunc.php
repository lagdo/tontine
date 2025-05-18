<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;

class MemberFunc extends FuncComponent
{
    use AmountTrait;

    /**
     * @before checkChargeEdit
     *
     * @param int $memberId
     * @param bool $paid
     *
     * @return mixed
     */
    public function addBill(int $memberId, bool $paid)
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->createBill($round, $charge, $session, $memberId, $paid);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     *
     * @param int $memberId
     *
     * @return mixed
     */
    public function delBill(int $memberId)
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->deleteBill($round, $charge, $session, $memberId);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }
}
