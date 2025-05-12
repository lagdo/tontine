<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\PageComponent;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

/**
 * @databag planning.pool
 */
class PoolPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.pool', 'pool.page'];

    public function __construct(private PoolService $poolService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->tenantService->round();
        $filter = $this->bag('planning.pool')->get('pool.filter', null);
        return $this->poolService->getPoolDefCount($round, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->tenantService->round();
        $filter = $this->bag('planning.pool')->get('pool.filter', null);
        return $this->renderView('pages.planning.pool.page', [
            'round' => $round,
            'defs' => $this->poolService->getPoolDefs($round, $filter, $this->currentPage()),
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
