<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;
use Stringable;

class Loan extends Component
{
    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(protected MemberService $memberService,
        protected SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');

        if(!$member)
        {
            return $this->renderView('pages.report.session.session.loans', [
                'loan' => $this->sessionService->getLoan($session),
            ]);
        }
        return $this->renderView('pages.report.session.member.loans', [
            'loans' => $this->memberService->getLoans($session, $member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('report-loans');
    }
}
