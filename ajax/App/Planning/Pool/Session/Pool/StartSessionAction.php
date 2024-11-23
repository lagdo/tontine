<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

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
            'pool' => $this->cache->get('pool.session.pool'),
        ]);
    }
}
