<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class Total extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.deposit.receivable.total', [
            'depositCount' => $this->stash()->get('meeting.pool.deposit.count'),
            'depositAmount' => $this->stash()->get('meeting.pool.deposit.amount'),
        ]);
    }
}
