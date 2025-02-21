<?php

namespace Ajax\App\Meeting\Session\Credit;

use Siak\Tontine\Service\Tontine\FundService;

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
        // Try to get the selected savings fund.
        $fund = null;
        $fundId = $this->bag('refund')->get('fund.id', 0);
        if($fundId !== 0)
        {
            if(($fund = $this->fundService->getFund($fundId, true)) === null)
            {
                $fundId = 0;
            }
        }
        if($fundId === 0)
        {
            // If not found, then revert to the tontine default fund.
            $fund = $this->fundService->getDefaultFund();
            $this->bag('refund')->set('fund.id', $fund->id);
        }
        $this->stash()->set('meeting.refund.fund', $fund);
    }
}
