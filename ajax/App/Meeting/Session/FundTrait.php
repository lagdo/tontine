<?php

namespace Ajax\App\Meeting\Session;

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
     * @param bool $skipSave
     *
     * @return void
     */
    protected function getFund(bool $skipSave = false): void
    {
        if(!$skipSave && $this->target()->method() === 'fund')
        {
            // Save the fund id in the databag.
            $this->bag($this->bagId)->set('fund.id', $this->target()->args()[0]);
        }

        $session = $this->stash()->get('meeting.session');
        $fundId = $this->bag($this->bagId)->get('fund.id', 0);
        $fund = $this->fundService->getSessionFund($session, $fundId);
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
