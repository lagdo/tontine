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
        return $this->poolService->getPoolCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.pool.page', [
            'round' => $this->tenantService->round(),
            'pools' => $this->poolService->getPools($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-planning-pool-page');
    }
}
