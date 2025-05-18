<?php

namespace Ajax\App\Planning\Charge;

use Ajax\App\Planning\PageComponent;
use Siak\Tontine\Service\Planning\ChargeService;
use Stringable;

/**
 * @databag planning.charge
 */
class ChargePage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.charge', 'page'];

    public function __construct(private ChargeService $chargeService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('tenant.round');
        $filter = $this->bag('planning.charge')->get('filter', null);
        return $this->chargeService->getChargeDefCount($round, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('tenant.round');
        $filter = $this->bag('planning.charge')->get('filter', null);
        return $this->renderView('pages.planning.charge.page', [
            'round' => $round,
            'defs' => $this->chargeService->getChargeDefs($round, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-planning-charge-page');
    }
}
