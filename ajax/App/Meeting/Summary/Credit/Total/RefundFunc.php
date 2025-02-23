<?php

namespace Ajax\App\Meeting\Summary\Credit\Total;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag refund
 * @before getFund
 */
class RefundFunc extends FuncComponent
{
    /**
     * The constructor
     *
     * @param FundService $fundService
     */
    public function __construct(protected FundService $fundService)
    {}

    protected function getFund()
    {
        // Try to get the selected savings fund.
        // If not found, then revert to the tontine default fund.
        $fundId = $this->bag('refund')->get('fund.id', 0);
        if($fundId !== 0 && ($fund = $this->fundService->getFund($fundId, true)) === null)
        {
            $fundId = 0;
        }
        if($fundId === 0)
        {
            $fund = $this->fundService->getDefaultFund();
            $this->bag('refund')->set('fund.id', $fund->id);
        }

        $this->stash()->set('summary.refund.fund', $fund);
    }

    public function toggleFilter()
    {
        $filtered = $this->bag('refund')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('refund')->set('filter', $filtered);

        $this->cl(RefundPage::class)->page();
    }
}
