<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Request\CallableAction;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Service\Meeting\Saving\FundService;

trait FundTrait
{
    /**
     * @var FundService
     */
    #[Inject]
    protected FundService $fundService;

    /**
     * Get the Jaxon request target
     *
     * @return CallableAction|null
     */
    abstract protected function action(): ?CallableAction;

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
        if($this->action()->func() === 'fund')
        {
            // Save the fund id in the databag.
            $this->bag($this->bagId)->set('fund.id', $this->action()->args()[0]);
        }

        $session = $this->stash()->get('meeting.session');
        $fund = null;
        $fundId = $this->bag($this->bagId)->get('fund.id', 0);
        if($fundId > 0)
        {
            $fund = $this->fundService->getSessionFund($session, $fundId, false);
        }
        if($fund === null)
        {
            $this->bag($this->bagId)->set('fund.id', 0);
        }
        $this->stash()->set("{$this->bagId}.fund", $fund);
    }

    /**
     * @return Fund|null
     */
    protected function getStashedFund(): ?Fund
    {
        return $this->stash()->get("{$this->bagId}.fund");
    }
}
