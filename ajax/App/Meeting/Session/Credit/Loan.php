<?php

namespace Ajax\App\Meeting\Session\Credit;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

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
        $session = $this->stash()->get('meeting.session');
        return $this->renderView('pages.meeting.loan.home', [
            'session' => $session,
            'loans' => $this->loanService->getSessionLoans($session),
            'defaultFund' => $this->fundService->getDefaultFund(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Balance::class)->render();
        $this->response->js('Tontine')->makeTableResponsive('content-session-loans');
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
