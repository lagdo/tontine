<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\Component;

/**
 * @exclude
 */
class PoolRoundAction extends Component
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
