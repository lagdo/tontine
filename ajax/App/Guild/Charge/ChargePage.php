<?php

namespace Ajax\App\Guild\Charge;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\ChargeService;
use Stringable;

/**
 * @databag charge
 * @before checkHostAccess ["finance", "charges"]
 */
class ChargePage extends PageComponent
{
    use ChargeTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['charge', 'page'];

    /**
     * @param ChargeService $chargeService
     */
    public function __construct(protected ChargeService $chargeService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->chargeService->getChargeCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.charge.page', [
            'types' => $this->getChargeTypes(),
            'periods' => $this->getChargePeriods(),
            'charges' => $this->chargeService->getCharges($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-charge-page');
    }
}
