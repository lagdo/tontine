<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\Component;

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
        $pool = $this->cache()->get('pool.session.pool');

        return $pool->pool_round !== null ? $pool->start_date :
            trans('tontine.pool_round.labels.default');
    }
}
