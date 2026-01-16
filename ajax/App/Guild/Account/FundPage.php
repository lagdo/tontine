<?php

namespace Ajax\App\Guild\Account;

use Ajax\Base\Guild\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\FundService;

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
        return $this->fundService->getFundCount($this->guild());
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.guild.account.fund.page', [
            'funds' => $this->fundService->getFunds($this->guild(), $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-fund-page');
    }
}
