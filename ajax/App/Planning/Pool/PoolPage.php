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
    protected array $bagOptions = ['planning.pool', 'page'];

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
        $round = $this->stash()->get('tenant.round');
        $filter = $this->bag('planning.pool')->get('filter', null);
        return $this->poolService->getPoolDefCount($round, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('tenant.round');
        $filter = $this->bag('planning.pool')->get('filter', null);
        return $this->renderView('pages.planning.pool.page', [
            'round' => $round,
            'defs' => $this->poolService->getPoolDefs($round, $filter, $this->currentPage()),
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
