<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\Cache;
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
            'pool' => Cache::get('planning.pool'),
        ]);
    }

    public function pool(PoolModel $pool): ComponentResponse
    {
        Cache::set('planning.pool', $pool);

        return $this->render();
    }
}
