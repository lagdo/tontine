<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\PageComponent;
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

    public function html(): string
    {
        return (string)$this->renderView('pages.planning.pool.page', [
            'round' => $this->tenantService->round(),
            'pools' => $this->poolService->getPools($this->page),
        ]);
    }

    protected function count(): int
    {
        return $this->poolService->getPoolCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('content-page');

        return $this->response;
    }
}
