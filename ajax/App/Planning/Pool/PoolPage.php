<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\PageComponent;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

/**
 * @databag planning.finance.pool
 */
class PoolPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['pool', 'page'];

    public function __construct(private PoolService $poolService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->tenantService->round();
        $filter = $this->bag('planning.finance.pool')->get('filter', null);
        return $this->poolService->getPoolDefCount($round, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->tenantService->round();
        $filter = $this->bag('planning.finance.pool')->get('filter', null);
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
