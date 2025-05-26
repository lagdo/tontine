<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;
use Stringable;

/**
 * @exclude
 */
class Saving extends Component
{
    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     * @param FundService $fundService
     */
    public function __construct(protected MemberService $memberService,
        protected SessionService $sessionService, protected FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');

        if($member !== null)
        {
            return $this->renderView('pages.report.session.member.savings', [
                'savings' => $this->memberService->getSavings($session, $member),
            ]);
        }
        return $this->renderView('pages.report.session.session.savings', [
            'saving' => $this->sessionService->getSaving($session),
            'funds' => $this->fundService->getSessionFundList($session),
            'startingFunds' => $this->fundService->getStartingFunds($session),
            'endingFunds' => $this->fundService->getEndingFunds($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-report-savings');
    }
}
