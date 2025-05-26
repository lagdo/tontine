<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\Component;
use Stringable;

/**
 * @databag planning.pool
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
        $filter = $this->bag('planning.pool')->get('filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.pool')->set('filter', $filter);
        $this->bag('planning.pool')->set('page', 1);

        $this->cl(PoolPage::class)->page();
    }
}
