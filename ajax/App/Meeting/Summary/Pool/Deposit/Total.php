<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit;

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
        return $this->renderTpl('pages.meeting.summary.deposit.receivable.total', [
            'depositCount' => $this->stash()->get('summary.pool.deposit.count'),
            'depositAmount' => $this->stash()->get('summary.pool.deposit.amount'),
        ]);
    }
}
