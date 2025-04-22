<?php

namespace Ajax\App\Planning\Finance\Fund;

use Siak\Tontine\Service\Planning\FundService;

trait FundTrait
{
    /**
     * @di
     * @var FundService
     */
    protected FundService $fundService;

    /**
     * @return void
     */
    protected function getFund()
    {
        if($this->target()->method() === 'fund')
        {
            $fundId = $this->target()->args()[0];
            $this->bag('planning.finance.fund')->set('fund.id', $fundId);
        }

        $round = $this->tenantService->round();
        $fundId = (int)$this->bag('planning.finance.fund')->get('fund.id');
        $fund = $this->fundService->getFund($round, $fundId);
        $this->stash()->set('planning.finance.fund', $fund);
    }
}
