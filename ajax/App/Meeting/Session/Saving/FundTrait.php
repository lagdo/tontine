<?php

namespace Ajax\App\Meeting\Session\Saving;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Request\TargetInterface;
use Siak\Tontine\Model\Fund;

use function in_array;

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
        if(in_array($this->target()->method(),
            ['fund', 'editStartAmount', 'editEndAmount']))
        {
            $fundId = $this->target()->args()[0];
            $this->bag('meeting.saving')->set('fund.id', $fundId);
        }

        $session = $this->stash()->get('meeting.session');
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->savingService->getFund($session, $fundId);
        if($fund === null)
        {
            $this->bag('meeting.saving')->set('fund.id', 0);
        }
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    /**
     * @return Fund|null
     */
    protected function getStashedFund(): ?Fund
    {
        return $this->stash()->get('meeting.saving.fund');
    }
}
