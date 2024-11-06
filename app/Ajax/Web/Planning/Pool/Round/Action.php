<?php

namespace App\Ajax\Web\Planning\Pool\Round;

use App\Ajax\Component;

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
        return (string)$this->renderView('pages.planning.pool.round.actions', [
            'pool' => $this->cache->get('planning.pool'),
        ]);
    }
}
