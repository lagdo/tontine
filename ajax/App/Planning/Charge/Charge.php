<?php

namespace Ajax\App\Planning\Charge;

use Ajax\App\Planning\Component;
use Stringable;

/**
 * @databag planning.charge
 */
class Charge extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.charge.home', [
            'guild' => $this->tenantService->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(ChargePage::class)->page();
    }

    public function toggleFilter()
    {
        // Toggle the filter
        $filter = $this->bag('planning.charge')->get('filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.charge')->set('filter', $filter);
        $this->bag('planning.charge')->set('page', 1);

        $this->cl(ChargePage::class)->page();
    }
}
