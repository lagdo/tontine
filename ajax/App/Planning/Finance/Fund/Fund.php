<?php

namespace Ajax\App\Planning\Finance\Fund;

use Ajax\Component;
use Stringable;

/**
 * @databag fund
 */
class Fund extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.finance.fund.home', [
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
