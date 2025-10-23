<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit;

use Ajax\Component;
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
        return $this->renderView('pages.meeting.summary.deposit.receivable.total', [
            'depositCount' => $this->stash()->get('summary.pool.deposit.count'),
            'depositAmount' => $this->stash()->get('summary.pool.deposit.amount'),
        ]);
    }
}
