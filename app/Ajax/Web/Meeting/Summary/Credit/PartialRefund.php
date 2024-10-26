<?php

namespace App\Ajax\Web\Meeting\Summary\Credit;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag refund.partial
 */
class PartialRefund extends Component
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
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.summary.refund.partial.home', [
            'session' => Cache::get('summary.session'),
            'funds' => $this->fundService->getFundList(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund(0);
        $this->cl(PartialRefundPage::class)->page();
    }

    protected function getFund()
    {
        // Try to get the selected savings fund.
        // If not found, then revert to the tontine default fund.
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

        Cache::set('summary.refund.partial.fund', $fund);
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

        $this->cl(PartialRefundPage::class)->page();
    }
}
