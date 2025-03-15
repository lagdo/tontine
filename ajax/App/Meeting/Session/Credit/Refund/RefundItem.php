<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\Component;
use Stringable;

/**
 * @exclude
 */
class RefundItem extends Component
{
    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund';

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.refund.item', [
            'session' => $this->stash()->get('meeting.session'),
            'debt' => $this->stash()->get('meeting.refund.debt'),
        ]);
    }
}
