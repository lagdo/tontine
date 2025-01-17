<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

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

        if(!$member)
        {
            return $this->renderView('pages.report.session.session.savings', [
                'saving' => $this->sessionService->getSaving($session),
                'funds' => $this->fundService->getFundList(),
            ]);
        }
        return $this->renderView('pages.report.session.member.savings', [
            'savings' => $this->memberService->getSavings($session, $member),
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
