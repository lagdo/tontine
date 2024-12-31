<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\ChargeComponent;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\LocaleService;
use Stringable;

use function Jaxon\jq;
use function Jaxon\pm;

class Amount extends ChargeComponent
{
    use AmountTrait;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     */
    public function __construct(protected LocaleService $localeService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $bill = $this->stash()->get('meeting.charge.bill');
        $memberId = $this->stash()->get('meeting.charge.member.id');

        if(!$session->opened || !$charge->is_active)
        {
            return $this->renderView('pages.meeting.charge.libre.member.closed', [
                'amount' => !$bill ? '' : $this->localeService->formatMoney($bill->amount, true),
            ]);
        }

        // When editing the bill amount, or when there is no bill yet,
        // then show the amount edit form.
        $edit = $this->stash()->get('meeting.charge.edit');
        if($edit || !$bill)
        {
            $amountValue = jq("#member-charge-input-$memberId")->val();
            $paid = pm()->checked('check-fee-libre-paid');

            return $this->renderView('pages.meeting.charge.libre.member.edit', [
                'memberId' => $memberId,
                'amount' => !$bill ? '' : $this->localeService->getMoneyValue($bill->amount),
                'handler' => $this->rq()->save($memberId, $paid, $amountValue),
            ]);
        }

        return $this->renderView('pages.meeting.charge.libre.member.show', [
            'memberId' => $memberId,
            'amount' => $this->localeService->formatMoney($bill->amount, false),
            'rqAmount' => $this->rq(),
        ]);
    }

    /**
     * @param int $memberId
     *
     * @return AjaxResponse
     */
    private function refresh(int $memberId): AjaxResponse
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $bill = $this->billService->getMemberBill($charge, $session, $memberId);
        if($bill === null)
        {
            return $this->response;
        }

        $this->stash()->set('meeting.charge.bill', $bill->bill);
        $this->stash()->set('meeting.charge.member.id', $memberId);

        return $this->item($memberId)->render();
    }

    /**
     * @before checkChargeEdit
     * @param int $memberId
     *
     * @return AjaxResponse
     */
    public function edit(int $memberId): AjaxResponse
    {
        $this->stash()->set('meeting.charge.edit', true);

        return $this->refresh($memberId);
    }

    /**
     * @param int $memberId
     * @param bool $paid
     * @param string $amount
     *
     * @return void
     */
    private function saveAmount(int $memberId, bool $paid, string $amount): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $amount = $this->convertAmount($amount);

        if(!$amount)
        {
            // No amount provided => the bill is deleted.
            $this->billService->deleteBill($charge, $session, $memberId);
            return;
        }

        $bill = $this->billService->getMemberBill($charge, $session, $memberId);
        if($bill !== null)
        {
            // The bill exists => it is updated.
            $this->billService->updateBill($charge, $session, $memberId, $amount);
            return;
        }

        // The bill is created.
        $this->billService->createBill($charge, $session, $memberId, $paid, $amount);
    }

    /**
     * @before checkChargeEdit
     *
     * @param int $memberId
     * @param bool $paid
     * @param string $amount
     *
     * @return AjaxResponse
     */
    public function save(int $memberId, bool $paid, string $amount): AjaxResponse
    {
        $this->saveAmount($memberId, $paid, $amount);
        $this->showTotal();

        return $this->refresh($memberId);
    }
}
