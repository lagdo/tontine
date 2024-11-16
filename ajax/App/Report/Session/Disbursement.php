<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;

class Disbursement extends Component
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
    public function html(): string
    {
        $session = $this->cache->get('report.session');
        $member = $this->cache->get('report.member');

        if(!$member)
        {
            return $this->renderView('pages.report.session.session.disbursements', [
                'disbursement' => $this->sessionService->getDisbursement($session),
            ]);
        }
        return $this->renderView('pages.report.session.member.disbursements', [
            'disbursements' => $this->memberService->getDisbursements($session, $member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-disbursements');
    }
}