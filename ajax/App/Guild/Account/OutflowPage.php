<?php

namespace Ajax\App\Guild\Account;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\AccountService;
use Stringable;

/**
 * @databag tontine
 * @before checkHostAccess ["finance", "accounts"]
 */
class OutflowPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'outflow.page'];

    /**
     * @param AccountService $accountService
     */
    public function __construct(protected AccountService $accountService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->accountService->getCategoryCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.account.outflow.page', [
            'accounts' => $this->accountService->getAccounts($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-category-page');
    }
}
