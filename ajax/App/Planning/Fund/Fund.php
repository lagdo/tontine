<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Stringable;

/**
 * @databag planning.fund
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

    public function toggleFilter()
    {
        // Toggle the filter
        $filter = $this->bag('planning.fund')->get('filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.fund')->set('filter', $filter);
        $this->bag('planning.fund')->set('page', 1);

        $this->cl(FundPage::class)->page();
    }
}
