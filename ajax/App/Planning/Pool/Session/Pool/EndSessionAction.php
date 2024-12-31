<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class EndSessionAction extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.end.actions', [
            'pool' => $this->stash()->get('pool.session.pool'),
        ]);
    }
}
