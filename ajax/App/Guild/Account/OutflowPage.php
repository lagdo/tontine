<?php

namespace Ajax\App\Guild\Account;

use Ajax\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\AccountService;
use Stringable;

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
        $guild = $this->stash()->get('tenant.guild');
        return $this->accountService->getCategoryCount($guild);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->renderView('pages.guild.account.outflow.page', [
            'accounts' => $this->accountService->getAccounts($guild, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-outflow-page');
    }
}
