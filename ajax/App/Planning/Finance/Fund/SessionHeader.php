<?php

namespace Ajax\App\Planning\Finance\Fund;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class SessionHeader extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.finance.fund.session.header', [
            'fund' => $this->stash()->get('planning.finance.fund'),
        ]);
    }
}
