<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('checkChargeEdit')]
class MemberFunc extends FuncComponent
{
    use AmountTrait;

    /**
     * @param int $memberId
     * @param bool $paid
     *
     * @return mixed
     */
    public function createBill(int $memberId, bool $paid): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->createBill($charge, $session, $memberId, $paid);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function deleteBill(int $memberId): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->billService->deleteBill($charge, $session, $memberId);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @return mixed
     */
    public function confirmAll(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('fee.member.search', '');

        $noBillCount = $this->billService->getMemberCount($charge, $session,
            $search, false);
        if($noBillCount < 2)
        {
            return;
        }

        $title = trans('meeting.bill.titles.all', ['count' => $noBillCount]);
        $content = $this->renderView('pages.meeting.session.charge.libre.member.confirm', [
            'charge' => $charge,
        ]);

        $formValues = je('bill-all-form')->rd()->form();
        $paid = je('check-fee-libre-paid')->rd()->checked();
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createBills($formValues, $paid),
        ]];

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @param array $formValues
     * @param bool $paid
     *
     * @return mixed
     */
    public function createBills(array $formValues, bool $paid): void
    {
        $charge = $this->stash()->get('meeting.session.charge');
        $amount = $charge->has_amount ? 0 :
            $this->convertAmount($formValues['amount'] ?? '', true);

        $session = $this->stash()->get('meeting.session');
        $search = $this->bag('meeting')->get('fee.member.search', '');
        $this->billService->createBills($charge, $session,
            $search, $paid, $amount);

        $this->modal()->hide();
        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @return mixed
     */
    public function deleteBills(): void
    {
        $charge = $this->stash()->get('meeting.session.charge');
        $session = $this->stash()->get('meeting.session');
        $search = $this->bag('meeting')->get('fee.member.search', '');
        $this->billService->deleteBills($charge, $session, $search);

        $this->showTotal();
        $this->cl(MemberPage::class)->page();
    }
}
