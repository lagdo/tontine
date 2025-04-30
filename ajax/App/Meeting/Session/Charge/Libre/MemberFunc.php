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
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->createBill($charge, $session, $memberId, $paid);

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
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->deleteBill($charge, $session, $memberId);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }
}
