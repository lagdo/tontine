<?php

namespace Ajax\App\Meeting\Summary\Credit\Loan;

use Ajax\App\Meeting\Summary\PageComponent;
use Siak\Tontine\Service\Meeting\Credit\LoanService;

class LoanPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary', 'loan.page'];

    /**
     * The constructor
     *
     * @param LoanService $loanService
     */
    public function __construct(private LoanService $loanService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('summary.session');
        return $this->loanService->getSessionLoanCount($session);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderTpl('pages.meeting.summary.loan.page', [
            'session' => $session,
            'loans' => $this->loanService->getSessionLoans($session, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-session-loans-page');
    }
}
