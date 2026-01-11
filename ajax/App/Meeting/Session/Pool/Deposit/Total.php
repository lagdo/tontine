<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
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
