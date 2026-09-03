<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Guild\PoolService as GuildPoolService;
use Siak\Tontine\Service\Planning\PoolService;

#[Exclude]
class PoolCount extends Component
{
    public function __construct(private PoolService $poolService,
        private GuildPoolService $guildPoolService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.pool.count', [
            'count' => $this->poolService->getPoolCount($this->round()),
            'total' => $this->guildPoolService->getPoolCount($this->guild()),
        ]);
    }
}
