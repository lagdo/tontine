<?php

namespace Ajax\App\Planning\Pool\Round;

use Ajax\Component;

/**
 * @exclude
 */
class Action extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.planning.pool.round.actions', [
            'pool' => $this->cache->get('planning.pool'),
        ]);
    }
}
