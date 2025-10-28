<?php

namespace Ajax\App\Meeting\Summary\Credit\Loan;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Stringable;

#[Export(base: ['render'], except: ['show'])]
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
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.loan.home', [
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

    public function show(): void
    {
        $this->render();
    }
}
