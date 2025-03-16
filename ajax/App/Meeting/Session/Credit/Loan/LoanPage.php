<?php

namespace Ajax\App\Meeting\Session\Credit\Loan;

use Ajax\App\Meeting\PageComponent;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Stringable;

/**
 * @databag meeting.loan
 */
class LoanPage extends PageComponent
{
    /**
     * @var string
     */
    protected string $bagId = 'meeting.loan';

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.loan', 'page'];

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
        $session = $this->stash()->get('meeting.session');
        return $this->loanService->getSessionLoanCount($session);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        return $this->renderView('pages.meeting.loan.page', [
            'session' => $session,
            'loans' => $this->loanService->getSessionLoans($session, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-loans-page');
    }
}
