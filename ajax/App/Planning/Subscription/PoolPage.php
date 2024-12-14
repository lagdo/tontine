<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

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
    protected array $bagOptions = ['subscription', 'pool.page'];

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
        $pools = $this->poolService->getPools($this->pageNumber());
        // When showing the page for the first time, we'll need to get the first pool
        $this->cache()->set('subscription.pools', $pools);

        return $this->renderView('pages.planning.subscription.pool.page', [
            'round' => $this->tenantService->round(),
            'pools' => $pools,
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
