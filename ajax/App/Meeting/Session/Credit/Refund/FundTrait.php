<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Siak\Tontine\Model\Fund;
use Siak\Tontine\Service\Meeting\FundService;

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
    protected function getFund(): void
    {
        if($this->target()->method() === 'fund')
        {
            // Save the fund id in the databag.
            $this->bag($this->bagId)->set('fund.id', $this->target()->args()[0]);
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
