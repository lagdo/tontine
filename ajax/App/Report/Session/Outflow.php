<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;

#[Exclude]
class Outflow extends Component
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
            return $this->renderTpl('pages.report.session.session.outflows', [
                'outflow' => $this->sessionService->getOutflow($session),
            ]);
        }
        return $this->renderTpl('pages.report.session.member.outflows', [
            'outflows' => $this->memberService->getOutflows($session, $member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-report-outflows');
    }
}
