<?php

namespace Ajax\App\Guild\Charge;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\ChargeService;
use Stringable;

/**
 * @databag guild.charge
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
    protected array $bagOptions = ['guild.charge', 'page'];

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
        $guild = $this->stash()->get('tenant.guild');
        $filter = $this->bag('guild.charge')->get('filter', null);

        return $this->chargeService->getChargeCount($guild, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guild = $this->stash()->get('tenant.guild');
        $filter = $this->bag('guild.charge')->get('filter', null);

        return $this->renderView('pages.guild.charge.page', [
            'types' => $this->getChargeTypes(),
            'periods' => $this->getChargePeriods(),
            'charges' => $this->chargeService
                ->getCharges($guild, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-charge-page');
    }
}
