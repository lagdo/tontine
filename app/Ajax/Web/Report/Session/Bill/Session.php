<?php

namespace App\Ajax\Web\Report\Session\Bill;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;

use function trans;

class Session extends Component
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
            return $this->renderView('pages.report.session.session.bills', [
                'title' => trans('tontine.report.titles.bills.session'),
                'charges' => $this->sessionService->getSessionCharges($session),
            ]);
        }
        return $this->renderView('pages.report.session.member.bills.session', [
            'bills' => $this->memberService->getBills($session, $member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-session-bills');
    }
}
