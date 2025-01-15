<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class StartSessionAction extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.start.actions', [
            'pool' => $this->stash()->get('pool.session.pool'),
        ]);
    }
}
