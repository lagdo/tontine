<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;

class MemberFunc extends FuncComponent
{
    use AmountTrait;

    /**
     * @param int $memberId
     * @param bool $paid
     *
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function addBill(int $memberId, bool $paid): void
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->createBill($round, $charge, $session, $memberId, $paid);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function delBill(int $memberId): void
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->deleteBill($round, $charge, $session, $memberId);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }
}
