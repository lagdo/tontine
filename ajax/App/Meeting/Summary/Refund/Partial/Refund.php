<?php

namespace Ajax\App\Meeting\Summary\Refund\Partial;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Meeting\FundService;
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
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.refund.partial.home', [
            'session' => $session,
            'funds' => $this->fundService->getSessionFundList($session),
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
        $session = $this->stash()->get('summary.session');
        $fundId = $this->bag('refund.partial')->get('fund.id', 0);
        $fund = $this->fundService->getSessionFund($session, $fundId);

        $this->bag('refund.partial')->set('fund.id', $fund?->id ?? 0);
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
