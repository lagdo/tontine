<?php

namespace Ajax\App\Planning\Pool;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag pool
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
        return $this->poolService->getPoolCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.planning.pool.page', [
            'round' => $this->tenantService->round(),
            'pools' => $this->poolService->getPools($this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
    }
}
