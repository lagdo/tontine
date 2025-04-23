<?php

namespace Ajax\App\Planning\Finance\Pool;

use Ajax\Component;
use Stringable;

/**
 * @databag planning.finance.pool
 */
class Pool extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.finance.pool.home', [
            'guild' => $this->tenantService->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->page();
    }
}
