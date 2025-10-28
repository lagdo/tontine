<?php

namespace Ajax\App\Meeting\Session\Credit\Loan;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Stringable;

use function trans;

#[Export(base: ['render'])]
class Balance extends Component
{
    /**
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private BalanceCalculator $balanceCalculator)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        return $this->renderView('pages.meeting.session.loan.balance', [
            'rqBalance' => $this->rq(),
            'amount' => $this->balanceCalculator->getBalanceForLoan($session),
        ]);
    }

    public function details(): void
    {
        $session = $this->stash()->get('meeting.session');
        $balances = $this->balanceCalculator->getBalances($session, true);
        $title = trans('meeting.titles.amounts');
        $content = $this->renderView('pages.meeting.session.balances', [
            'session' => $session,
            'balances' => $balances,
        ]);
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ]];
        $this->modal()->show($title, $content, $buttons);
    }
}
