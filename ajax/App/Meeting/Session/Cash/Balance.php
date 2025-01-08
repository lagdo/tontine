<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\BalanceCalculator;
use Stringable;

use function trans;

class Balance extends MeetingComponent
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
        return $this->renderView('pages.meeting.disbursement.balance', [
            'rqBalance' => $this->rq(),
            'amount' => $this->balanceCalculator->getTotalBalance($session),
        ]);
    }

    public function details()
    {
        $session = $this->stash()->get('meeting.session');
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
