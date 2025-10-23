<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;
use Stringable;

#[Exclude]
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
    public function html(): Stringable
    {
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');

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
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-report-remitments');
    }
}
