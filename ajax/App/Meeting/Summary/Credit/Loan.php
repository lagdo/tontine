<?php

namespace Ajax\App\Meeting\Summary\Credit;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Tontine\FundService;
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
        $session = $this->stash()->get('summary.session');
        $loans = $this->loanService->getSessionLoans($session);

        return $this->renderView('pages.meeting.summary.loan.home', [
            'session' => $session,
            'loans' => $loans,
            'defaultFund' => $this->fundService->getDefaultFund(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-loans');
        $this->response->js()->showBalanceAmountsWithDelay();
    }
}
