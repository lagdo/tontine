<?php

namespace Ajax\App\Planning\Pool;

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
        return $this->renderView('pages.planning.pool.session.header', [
            'pool' => $this->stash()->get('planning.finance.pool'),
        ]);
    }
}
