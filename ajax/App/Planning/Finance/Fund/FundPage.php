<?php

namespace Ajax\App\Planning\Finance\Fund;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\FundService;
use Stringable;

/**
 * @databag fund
 */
class FundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['fund', 'page'];

    public function __construct(private FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->fundService->getFundDefCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->tenantService->round();
        return $this->renderView('pages.planning.finance.fund.page', [
            'round' => $round,
            'defs' => $this->fundService->getFundDefs($round, $this->currentPage()),
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
