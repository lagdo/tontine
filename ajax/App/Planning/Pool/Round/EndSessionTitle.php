<?php

namespace Ajax\App\Planning\Pool\Round;

use Ajax\Component;

use function trans;

/**
 * @exclude
 */
class EndSessionTitle extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->cache->get('planning.pool');

        return trans('tontine.pool_round.titles.end_session', [
            'session' => $pool->pool_round !== null ? $pool->end_date :
                trans('tontine.pool_round.labels.default'),
        ]);
    }
}
