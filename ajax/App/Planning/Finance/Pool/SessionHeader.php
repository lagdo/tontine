<?php

namespace Ajax\App\Planning\Finance\Pool;

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
        return $this->renderView('pages.planning.finance.pool.session.header', [
            'pool' => $this->stash()->get('planning.finance.pool'),
        ]);
    }
}
