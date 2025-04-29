<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\Session\Component;
use Siak\Tontine\Service\LocaleService;
use Stringable;

/**
 * @exclude
 */
class Amount extends Component
{
    /**
     * The constructor
     *
     * @param LocaleService $localeService
     */
    public function __construct(private LocaleService $localeService)
    {}

    public function html(): Stringable|string
    {
        $debt = $this->stash()->get('meeting.refund.debt');
        if(!$debt || !$debt->partial_refund)
        {
            return $this->renderView('pages.meeting.session.refund.amount.edit', [
                'debt' => $debt,
                'amount' => '',
            ]);
        }

        if($this->stash()->get('meeting.refund.edit', false))
        {
            return $this->renderView('pages.meeting.session.refund.amount.edit', [
                'debt' => $debt,
                'amount' => $this->localeService->getMoneyValue($debt->partial_refund->amount),
            ]);
        }

        return $this->renderView('pages.meeting.session.refund.amount.show', [
            'debt' => $debt,
            'amount' => $this->localeService->formatMoney($debt->partial_refund->amount, false),
        ]);
    }
}
