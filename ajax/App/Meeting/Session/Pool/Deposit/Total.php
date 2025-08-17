<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class Total extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.deposit.receivable.total', [
            'depositCount' => $this->stash()->get('meeting.pool.deposit.count'),
            'depositAmount' => $this->stash()->get('meeting.pool.deposit.amount'),
        ]);
    }
}
