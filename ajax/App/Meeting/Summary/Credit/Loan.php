<?php

namespace Ajax\App\Meeting\Summary\Credit;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Guild\FundService;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
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
        $this->response->js('Tontine')->makeTableResponsive('content-summary-loans');
    }
}
