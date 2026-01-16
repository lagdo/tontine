<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class Action extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.deposit.receivable.action', [
            'session' => $this->stash()->get('meeting.session'),
            'pool' => $this->stash()->get('meeting.pool'),
            'depositCount' => $this->stash()->get('meeting.pool.deposit.count'),
            'receivableCount' => $this->stash()->get('meeting.pool.deposit.total'),
        ]);
    }
}
