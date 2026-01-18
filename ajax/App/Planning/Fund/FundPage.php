<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\PageComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Planning\FundService;

#[Databag('planning.fund')]
class FundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.fund', 'fund.page'];

    /**
     * The constructor
     *
     * @param FundService $fundService
     */
    public function __construct(private FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $filter = $this->bag('planning.fund')->get('filter', null);
        return $this->fundService->getFundDefCount($this->round(), $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $filter = $this->bag('planning.fund')->get('filter', null);
        return $this->renderTpl('pages.planning.fund.page', [
            'round' => $this->round(),
            'defs' => $this->fundService->getFundDefs($this->round(), $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-planning-fund-page');
    }
}
