<?php

namespace Ajax\App\Planning\Pool;

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
        return $this->renderView('pages.planning.pool.home', [
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

    public function toggleFilter()
    {
        // Toggle the filter
        $filter = $this->bag('planning.finance.pool')->get('filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.finance.pool')->set('filter', $filter);
        $this->bag('planning.finance.pool')->set('page', 1);

        $this->cl(PoolPage::class)->page();
    }
}
