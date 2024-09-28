<?php

namespace App\Ajax\Web\Planning\Subscription;

use Jaxon\App\Component;
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
        $pool = $this->cl(Member::class)->getPool();

        return (string)$this->poolService->getEnabledSessionCount($pool);
    }
}
