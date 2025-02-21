<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;

use function trim;

class MemberFunc extends FuncComponent
{
    use AmountTrait;

    public function toggleFilter()
    {
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting')->set('fee.member.filter', $filter);

        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));

        $this->cl(MemberPage::class)->page();
    }

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
