<?php

namespace Ajax\App\Meeting\Summary\Refund\Partial;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag refund.partial
 */
class Refund extends Component
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param PartialRefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected PartialRefundService $refundService)
    {}

    /**
     * @exclude
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.refund.partial.home', [
            'session' => $this->stash()->get('summary.session'),
            'funds' => $this->fundService->getFundList(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund(0);
    }

    protected function getFund()
    {
        // Try to get the selected savings fund.
        // If not found, then revert to the guild default fund.
        $fundId = $this->bag('refund.partial')->get('fund.id', 0);
        if($fundId !== 0 && ($fund = $this->fundService->getFund($fundId, true)) === null)
        {
            $fundId = 0;
        }
        if($fundId === 0)
        {
            $fund = $this->fundService->getDefaultFund();
            $this->bag('refund.partial')->set('fund.id', $fund->id);
        }

        $this->stash()->set('summary.refund.partial.fund', $fund);
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('refund.partial')->set('fund.id', $fundId);
        $this->getFund();
        $this->cl(RefundPage::class)->page();
    }
}
