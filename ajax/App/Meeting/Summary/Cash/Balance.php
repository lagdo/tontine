<?php

namespace Ajax\App\Meeting\Summary\Cash;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Stringable;

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
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.outflow.balance', [
            'rqBalance' => $this->rq(),
            'amount' => $this->balanceCalculator->getTotalBalance($session),
        ]);
    }

    public function details()
    {
        $session = $this->stash()->get('summary.session');
        $balances = $this->balanceCalculator->getBalances($session, false);
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
