<?php

namespace Ajax\App\Meeting\Session\Credit\Loan;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Stringable;

class Loan extends Component
{
    /**
     * The constructor
     *
     * @param LoanService $loanService
     */
    public function __construct(private LoanService $loanService)
    {}

    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        return $this->renderView('pages.meeting.loan.home', [
            'session' => $session,
            'loans' => $this->loanService->getSessionLoans($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(LoanPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
