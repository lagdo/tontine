<?php

namespace Ajax\App\Guild\Pool;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\PoolService;
use Stringable;

/**
 * @databag guild.pool
 * @before checkHostAccess ["finance", "pools"]
 */
class PoolPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['guild.pool', 'page'];

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->poolService->getPoolCount($guild);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->renderView('pages.guild.pool.page', [
            'round' => $this->tenantService->round(),
            'pools' => $this->poolService->getPools($guild, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-planning-pool-page');
    }
}
