<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class ReceivableEdit extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.deposit.libre.edit', [
            'receivableId' => $this->cache->get('meeting.session.receivable.id'),
            'amount' => $this->cache->get('meeting.session.receivable.amount'),
        ]);
    }
}
