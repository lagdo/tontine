<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\Component;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Pool as PoolModel;

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

    public function pool(PoolModel $pool): ComponentResponse
    {
        $this->cache->set('planning.pool', $pool);

        return $this->render();
    }
}
