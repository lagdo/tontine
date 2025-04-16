<?php

namespace Ajax\App\Meeting\Summary\Credit;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Meeting\FundService;
use Stringable;

/**
 * @exclude
 */
class Loan extends Component
{
    /**
     * The constructor
     *
     * @param LoanService $loanService
     * @param FundService $fundService
     */
    public function __construct(protected LoanService $loanService,
        protected FundService $fundService)
    {}

    public function html(): Stringable
    {
        $round = $this->tenantService->round();
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.loan.home', [
            'session' => $session,
            'loans' => $this->loanService->getSessionLoans($session),
            'defaultFund' => $this->fundService->getDefaultFund($round),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-summary-loans');
    }
}
