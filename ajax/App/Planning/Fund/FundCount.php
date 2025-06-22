<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Siak\Tontine\Service\Planning\FundService;

/**
 * @exclude
 */
class FundCount extends Component
{
    public function __construct(private FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $round = $this->stash()->get('tenant.round');
        return $this->renderView('pages.planning.fund.count', [
            'count' => $this->fundService->getFundCount($round),
        ]);
    }
}
