<?php

namespace Ajax\App\Guild\Account;

use Ajax\Base\Guild\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\AccountService;

#[Before('checkHostAccess', ["finance", "accounts"])]
#[Databag('guild.account')]
class OutflowPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['guild.account', 'outflow.page'];

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
        return $this->accountService->getCategoryCount($this->guild());
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.guild.account.outflow.page', [
            'accounts' => $this->accountService->getAccounts($this->guild(), $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-outflow-page');
    }
}
