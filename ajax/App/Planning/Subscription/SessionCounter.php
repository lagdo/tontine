<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
use Siak\Tontine\Service\Planning\PoolService;

class SessionCounter extends Component
{
    /**
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    public function html(): string
    {
        $pool = $this->cache->get('subscription.pool');

        return (string)$this->poolService->getEnabledSessionCount($pool);
    }
}