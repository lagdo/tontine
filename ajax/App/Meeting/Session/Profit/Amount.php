<?php

namespace Ajax\App\Meeting\Session\Profit;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class Amount extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.profit.amount', [
            'fund' => $this->stash()->get('profit.fund'),
            'profitAmount' => $this->stash()->get('profit.amount'),
        ]);
    }
}
