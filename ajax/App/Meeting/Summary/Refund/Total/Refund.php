<?php

namespace Ajax\App\Meeting\Summary\Refund\Total;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\FundService;
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
     */
    public function __construct(protected FundService $fundService)
    {}

    /**
     * @exclude
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.refund.final.home', [
            'session' => $session,
            'funds' => $this->fundService->getSessionFundList($session),
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
        // If not found, then revert to the guild default fund.
        $session = $this->stash()->get('summary.session');
        $fundId = $this->bag('refund')->get('fund.id', 0);
        $fund = $this->fundService->getSessionFund($session, $fundId);

        $this->bag('refund')->set('fund.id', $fund?->id ?? 0);
        $this->stash()->set('summary.refund.fund', $fund);
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
}
