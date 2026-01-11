<?php

namespace Ajax\App\Planning\Charge;

use Ajax\App\Planning\PageComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Planning\ChargeService;
use Stringable;

#[Databag('planning.charge')]
class ChargePage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.charge', 'page'];

    /**
     * The constructor
     *
     * @param ChargeService $chargeService
     */
    public function __construct(private ChargeService $chargeService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $filter = $this->bag('planning.charge')->get('filter', null);
        return $this->chargeService->getChargeDefCount($this->round(), $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $filter = $this->bag('planning.charge')->get('filter', null);
        return $this->renderView('pages.planning.charge.page', [
            'round' => $this->round(),
            'defs' => $this->chargeService->getChargeDefs($this->round(), $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-planning-charge-page');
    }
}
