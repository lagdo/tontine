<?php

namespace Ajax\App\Planning\Fund;

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
        return $this->renderView('pages.planning.fund.session.header', [
            'fund' => $this->stash()->get('planning.finance.fund'),
        ]);
    }
}
