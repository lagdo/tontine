<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\PageComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

#[Databag('planning.pool')]
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
        $filter = $this->bag('planning.pool')->get('filter', null);
        return $this->poolService->getPoolDefCount($this->round(), $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $filter = $this->bag('planning.pool')->get('filter', null);
        return $this->renderView('pages.planning.pool.page', [
            'round' => $this->round(),
            'defs' => $this->poolService->getPoolDefs($this->round(), $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-planning-pool-page');
    }
}
