<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Siak\Tontine\Model\Fund;

trait FundTrait
{
    /**
     * @return void
     */
    protected function getFund(): void
    {
        if($this->target()->method() === 'fund')
        {
            $fundId = $this->target()->args()[0];
            $this->bag('summary.saving')->set('fund.id', $fundId);
        }

        $session = $this->stash()->get('summary.session');
        $fundId = $this->bag('summary.saving')->get('fund.id', 0);
        $fund = $this->savingService->getFund($session, $fundId);
        if($fund === null)
        {
            $this->bag('summary.saving')->set('fund.id', 0);
        }
        $this->stash()->set('summary.saving.fund', $fund);
    }

    /**
     * @return Fund|null
     */
    protected function getStashedFund(): ?Fund
    {
        return $this->stash()->get('summary.saving.fund');
    }
}
