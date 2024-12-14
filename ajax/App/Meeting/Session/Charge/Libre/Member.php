<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\ChargeComponent;
use Stringable;

use function trim;

class Member extends ChargeComponent
{
    use AmountTrait;

    /**
     * @var string
     */
    protected $overrides = Fee::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $charge = $this->cache()->get('meeting.session.charge');

        return $this->renderView('pages.meeting.charge.libre.member.home', [
            'charge' => $charge,
            'paid' => $charge->is_fee,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId)
    {
        $this->bag('meeting')->set('fee.member.filter', null);
        $this->bag('meeting')->set('fee.member.search', '');
        $this->bag('meeting')->set('fee.member.page', 1);

        return $this->render();
    }

    public function toggleFilter()
    {
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting')->set('fee.member.filter', $filter);

        return $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));

        return $this->cl(MemberPage::class)->page();
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
        $session = $this->cache()->get('meeting.session');
        $charge = $this->cache()->get('meeting.session.charge');
        $this->billService->createBill($charge, $session, $memberId, $paid);

        $this->showTotal();

        return $this->cl(MemberPage::class)->page();
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
        $session = $this->cache()->get('meeting.session');
        $charge = $this->cache()->get('meeting.session.charge');
        $this->billService->deleteBill($charge, $session, $memberId);

        $this->showTotal();

        return $this->cl(MemberPage::class)->page();
    }
}
