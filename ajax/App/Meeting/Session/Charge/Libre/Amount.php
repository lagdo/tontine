<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\Component;
use Siak\Tontine\Service\LocaleService;
use Stringable;

use function Jaxon\jq;
use function Jaxon\pm;

class Amount extends Component
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
                'handler' => $this->rq(AmountFunc::class)->save($memberId, $paid, $amountValue),
            ]);
        }

        return $this->renderView('pages.meeting.charge.libre.member.show', [
            'memberId' => $memberId,
            'amount' => $this->localeService->formatMoney($bill->amount, false),
            'rqAmountFunc' => $this->rq(AmountFunc::class),
        ]);
    }
}
