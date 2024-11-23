<?php

namespace Ajax\App\Meeting\Summary\Credit;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag refund
 */
class Refund extends Component
{
    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param RefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected RefundService $refundService)
    {}

    /**
     * @exclude
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.refund.home', [
            'session' => $this->cache->get('summary.session'),
            'funds' => $this->fundService->getFundList(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->getFund();
        $this->cl(RefundPage::class)->page();
    }

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

        $this->cache->set('summary.refund.fund', $fund);
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('refund')->set('fund.id', $fundId);
        $this->getFund();

        $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
     */
    public function toggleFilter()
    {
        $filtered = $this->bag('refund')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('refund')->set('filter', $filtered);

        $this->cl(RefundPage::class)->page();
    }
}
