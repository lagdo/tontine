<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\PageComponent;
use Siak\Tontine\Service\Planning\FundService;
use Stringable;

/**
 * @databag planning.fund
 */
class FundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.fund', 'fund.page'];

    public function __construct(private FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->tenantService->round();
        $filter = $this->bag('planning.fund')->get('filter', null);
        return $this->fundService->getFundDefCount($round, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->tenantService->round();
        $filter = $this->bag('planning.fund')->get('filter', null);
        return $this->renderView('pages.planning.fund.page', [
            'round' => $round,
            'defs' => $this->fundService->getFundDefs($round, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-planning-fund-page');
    }
}
