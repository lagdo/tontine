<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\Component;
use Siak\Tontine\Service\LocaleService;

use function Jaxon\checked;
use function jq;

class Amount extends Component
{
    use AmountTrait;
    use ChargeTrait;

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
    public function html(): string
    {
        $session = $this->stash()->get('meeting.session');
        $bill = $this->stash()->get('meeting.charge.bill');
        $member = $this->stash()->get('meeting.charge.member');
        $memberId = $member->id;

        if(!$session->opened)
        {
            return $this->renderTpl('pages.meeting.session.charge.libre.member.closed', [
                'amount' => !$bill ? '' : $this->localeService->formatMoney($bill->amount),
            ]);
        }

        // When editing the bill amount, or when there is no bill yet,
        // then show the amount edit form.
        $edit = $this->stash()->get('meeting.charge.edit');
        if(!$edit && $bill !== null)
        {
            return $this->renderTpl('pages.meeting.session.charge.libre.member.show', [
                'memberId' => $memberId,
                'amount' => $this->localeService->formatMoney($bill->amount, false),
                'rqAmountFunc' => $this->rq(AmountFunc::class),
            ]);
        }

        $amountValue = jq("#member-charge-input-$memberId")->val();
        $paid = checked('check-fee-libre-paid');
        return $this->renderTpl('pages.meeting.session.charge.libre.member.edit', [
            'memberId' => $memberId,
            'amount' => !$bill ? '' : $this->localeService->getMoneyValue($bill->amount),
            'handler' => $this->rq(AmountFunc::class)->save($memberId, $paid, $amountValue),
        ]);
    }
}
