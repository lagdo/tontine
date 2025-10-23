<?php

namespace Ajax\App\Meeting\Session\Credit\Loan;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Exclude;
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
        return $this->renderView('pages.meeting.session.loan.home', [
            'session' => $session,
            'loans' => $this->loanService->getSessionLoans($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(LoanPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    #[Exclude]
    public function show(): void
    {
        $this->render();
    }
}
