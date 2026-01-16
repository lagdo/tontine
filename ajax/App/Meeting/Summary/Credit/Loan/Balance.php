<?php

namespace Ajax\App\Meeting\Summary\Credit\Loan;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Payment\BalanceCalculator;

use function trans;

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
    public function html(): string
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderTpl('pages.meeting.summary.loan.balance', [
            'rqBalance' => $this->rq(),
            'amount' => $this->balanceCalculator->getBalanceForLoan($session),
        ]);
    }

    public function details(): void
    {
        $session = $this->stash()->get('summary.session');
        $balances = $this->balanceCalculator->getBalances($session, true);
        $title = trans('meeting.titles.amounts');
        $content = $this->renderTpl('pages.meeting.session.balances', [
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
