<?php

namespace Ajax\App\Guild\Charge;

use Ajax\Base\Guild\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\ChargeService;

#[Before('checkHostAccess', ["finance", "charges"])]
#[Databag('guild.charge')]
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
        $filter = $this->bag('guild.charge')->get('filter', null);

        return $this->chargeService->getChargeCount($this->guild(), $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $filter = $this->bag('guild.charge')->get('filter', null);

        return $this->renderTpl('pages.guild.charge.page', [
            'types' => $this->getChargeTypes(),
            'periods' => $this->getChargePeriods(),
            'charges' => $this->chargeService
                ->getCharges($this->guild(), $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-charge-page');
    }
}
