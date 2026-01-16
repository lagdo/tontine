<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Report\MemberService;

#[Exclude]
class Deposit extends Component
{
    /**
     * @param PoolService $poolService
     * @param MemberService $memberService
     */
    public function __construct(protected PoolService $poolService,
        protected MemberService $memberService)
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
            return $this->renderTpl('pages.report.session.session.deposits', [
                'pools' => $this->poolService->getPoolsWithReceivables($session),
            ]);
        }
        return $this->renderTpl('pages.report.session.member.deposits', [
            'receivables' => $this->memberService->getReceivables($session, $member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-report-deposits');
    }
}
