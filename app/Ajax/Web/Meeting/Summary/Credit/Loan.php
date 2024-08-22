<?php

namespace App\Ajax\Web\Meeting\Summary\Credit;

use App\Ajax\SessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;

/**
 * @exclude
 */
class Loan extends SessionCallable
{
    /**
     * The constructor
     *
     * @param LoanService $loanService
     * @param FundService $fundService
     * @param MemberService $memberService
     */
    public function __construct(protected LoanService $loanService,
        protected FundService $fundService, protected MemberService $memberService)
    {}

    public function show(SessionModel $session)
    {
        $this->session = $session;

        $loans = $this->loanService->getSessionLoans($this->session);

        $html = $this->renderView('pages.meeting.summary.loan.home', [
            'session' => $this->session,
            'loans' => $loans,
            'defaultFund' => $this->fundService->getDefaultFund(),
        ]);
        $this->response->html('meeting-loans', $html);
        $this->response->call('makeTableResponsive', 'meeting-loans');

        $this->response->call('showBalanceAmountsWithDelay');

        return $this->response;
    }
}
