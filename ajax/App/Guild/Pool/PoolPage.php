<?php

namespace Ajax\App\Guild\Pool;

use Ajax\Base\Guild\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\PoolService;

#[Before('checkHostAccess', ["finance", "pools"])]
#[Databag('guild.pool')]
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
        return $this->poolService->getPoolCount($this->guild());
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.guild.pool.page', [
            'round' => $this->tenantService->round(),
            'pools' => $this->poolService->getPools($this->guild(), $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-planning-pool-page');
    }
}
