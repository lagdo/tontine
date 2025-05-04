<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Stringable;

/**
 * @databag planning.finance.fund
 */
class Fund extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.fund.home', [
            'guild' => $this->tenantService->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(FundPage::class)->page();
    }
}
