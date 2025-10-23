<?php

namespace Ajax\App\Guild\Account;

use Ajax\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\FundService;
use Stringable;

#[Before('checkHostAccess', ["finance", "accounts"])]
#[Databag('guild.account')]
class FundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['guild.account', 'fund.page'];

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
        $guild = $this->stash()->get('tenant.guild');
        return $this->fundService->getFundCount($guild);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->renderView('pages.guild.account.fund.page', [
            'funds' => $this->fundService->getFunds($guild, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-fund-page');
    }
}
