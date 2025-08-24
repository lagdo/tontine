<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class Action extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.deposit.receivable.action', [
            'session' => $this->stash()->get('meeting.session'),
            'pool' => $this->stash()->get('meeting.pool'),
            'depositCount' => $this->stash()->get('meeting.pool.deposit.count'),
            'receivableCount' => $this->stash()->get('meeting.pool.deposit.total'),
        ]);
    }
}
