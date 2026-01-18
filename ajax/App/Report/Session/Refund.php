<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;

#[Exclude]
class Refund extends Component
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
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');

        if(!$member)
        {
            return $this->renderTpl('pages.report.session.session.refunds', [
                'refund' => $this->sessionService->getRefund($session),
            ]);
        }
        return $this->renderTpl('pages.report.session.member.refunds', [
            'refunds' => $this->memberService->getRefunds($session, $member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-report-refunds');
    }
}
