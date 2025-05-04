<?php

namespace Ajax\App\Guild\Account;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\FundService;
use Stringable;

/**
 * @databag tontine
 * @before checkHostAccess ["finance", "accounts"]
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
        return $this->renderView('pages.guild.account.fund.page', [
            'funds' => $this->fundService->getFunds($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-fund-page');
    }
}
