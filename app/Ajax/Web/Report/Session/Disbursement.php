<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\Cache;
use App\Ajax\Component;
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
        $session = Cache::get('report.session');
        $member = Cache::get('report.member');

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
