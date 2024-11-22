<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\Component;

/**
 * @exclude
 */
class StartSessionAction extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.planning.pool.session.start.actions', [
            'pool' => $this->cache->get('pool.session.pool'),
        ]);
    }
}
