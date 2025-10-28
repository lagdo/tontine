<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Databag('planning.fund')]
#[Export(base: ['render'])]
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
    protected function after(): void
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
