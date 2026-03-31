<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\App\ComponentDataTrait;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Exclude]
class Select extends Component
{
    use ComponentDataTrait;

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
        $sessions = $this->sessionService->getSessions($this->round(), orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));
        return $this->renderTpl('pages.report.session.select', [
            'sessions' => $sessions->pluck('title', 'id'),
            'members' => $this->memberService->getMemberList($this->round())->prepend('', 0),
            'content' => $this->get('content'),
        ]);
    }
}
