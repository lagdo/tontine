<?php

namespace Ajax\App\Planning\Charge;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Planning\ChargeService;

#[Exclude]
class ChargeCount extends Component
{
    public function __construct(private ChargeService $chargeService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $round = $this->stash()->get('tenant.round');
        return $this->renderView('pages.planning.charge.count', [
            'count' => $this->chargeService->getChargeCount($round),
        ]);
    }
}
