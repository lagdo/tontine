<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Request\TargetInterface;
use Siak\Tontine\Model\Fund;

trait FundTrait
{
    /**
     * Get the Jaxon request target
     *
     * @return TargetInterface|null
     */
    abstract protected function target(): ?TargetInterface;

    /**
     * Get the temp cache
     *
     * @return Stash
     */
    abstract protected function stash(): Stash;

    /**
     * Get a data bag.
     *
     * @param string  $sBagName
     *
     * @return DataBagContext
     */
    abstract protected function bag(string $sBagName): DataBagContext;

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
