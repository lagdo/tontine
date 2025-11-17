<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;
use Stringable;

#[Exclude]
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
                'transfers' => $this->memberService->getTransfers($session, $member),
            ]);
        }
        return $this->renderView('pages.report.session.session.savings', [
            'saving' => $this->sessionService->getSaving($session),
            'transfer' => $this->sessionService->getTransfer($session),
            'startingFunds' => $this->fundService->getStartingFunds($session),
            'endingFunds' => $this->fundService->getEndingFunds($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-report-savings');
    }
}
