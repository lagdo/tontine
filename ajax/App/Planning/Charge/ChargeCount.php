<?php

namespace Ajax\App\Planning\Charge;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Guild\ChargeService as GuildChargeService;
use Siak\Tontine\Service\Planning\ChargeService;

#[Exclude]
class ChargeCount extends Component
{
    public function __construct(private ChargeService $chargeService,
        private GuildChargeService $guildChargeService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.charge.count', [
            'count' => $this->chargeService->getChargeCount($this->round()),
            'total' => $this->guildChargeService->getChargeCount($this->guild()),
        ]);
    }
}
