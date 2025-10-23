<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Planning\PoolService;

#[Exclude]
class PoolCount extends Component
{
    public function __construct(private PoolService $poolService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $round = $this->stash()->get('tenant.round');
        return $this->renderView('pages.planning.pool.count', [
            'count' => $this->poolService->getPoolCount($round),
        ]);
    }
}
