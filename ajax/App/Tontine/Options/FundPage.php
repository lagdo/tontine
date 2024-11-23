<?php

namespace Ajax\App\Tontine\Options;

use Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag tontine
 */
class FundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'fund.page'];

    /**
     * @param FundService $fundService
     */
    public function __construct(protected FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->fundService->getFundCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.options.fund.page', [
            'funds' => $this->fundService->getFunds($this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('fund-page');
    }
}
