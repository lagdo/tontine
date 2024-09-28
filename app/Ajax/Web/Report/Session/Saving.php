<?php

namespace App\Ajax\Web\Report\Session;

use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;
use Siak\Tontine\Service\Tontine\FundService;

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
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.savings', [
                'saving' => $this->sessionService->getSaving($this->session),
                'funds' => $this->fundService->getFundList(),
            ]);
        }
        return $this->renderView('pages.report.session.member.savings', [
            'savings' => $this->memberService->getSavings($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-savings');
    }
}
