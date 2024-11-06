<?php

namespace App\Ajax\Web\Planning\Pool\Round;

use App\Ajax\Component;

use function trans;

/**
 * @exclude
 */
class StartSessionTitle extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->cache->get('planning.pool');

        return trans('tontine.pool_round.titles.start_session', [
            'session' => $pool->pool_round !== null ? $pool->start_date :
                trans('tontine.pool_round.labels.default'),
        ]);
    }
}
