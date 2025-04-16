<?php

namespace Ajax\App\Meeting\Summary\Refund\Total;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Meeting\FundService;

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
        // If not found, then revert to the guild default fund.
        $session = $this->stash()->get('summary.session');
        $fundId = $this->bag('refund')->get('fund.id', 0);
        $fund = $this->fundService->getSessionFund($session, $fundId);

        $this->bag('refund')->set('fund.id', $fund?->id ?? 0);
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
