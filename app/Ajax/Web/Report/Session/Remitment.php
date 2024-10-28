<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\Component;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;

class Remitment extends Component
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
            return $this->renderView('pages.report.session.session.remitments', [
                'pools' => $this->sessionService->getPayables($session),
                'auctions' => $this->sessionService->getAuctions($session),
            ]);
        }
        return $this->renderView('pages.report.session.member.remitments', [
            'payables' => $this->memberService->getPayables($session, $member),
            'auctions' => $this->memberService->getAuctions($session, $member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-remitments');
    }
}
