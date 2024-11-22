<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\Component;
use Siak\Tontine\Service\Planning\PoolService;

use function trans;

class SessionCounter extends Component
{
    /**
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    public function html(): string
    {
        $pool = $this->cache->get('pool.session.pool');

        return trans('tontine.pool_round.labels.sessions', [
            'count' => $this->poolService->getEnabledSessionCount($pool),
        ]);
    }
}
