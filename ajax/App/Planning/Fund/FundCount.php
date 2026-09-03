<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Guild\FundService as GuildFundService;
use Siak\Tontine\Service\Planning\FundService;

#[Exclude]
class FundCount extends Component
{
    public function __construct(private FundService $fundService,
        private GuildFundService $guildFundService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.fund.count', [
            'count' => $this->fundService->getFundCount($this->round()),
            'total' => $this->guildFundService->getFundCount($this->guild(), true),
        ]);
    }
}
