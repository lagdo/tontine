<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\Component;
use Siak\Tontine\Service\Planning\MemberService;

/**
 * @exclude
 */
class MemberCount extends Component
{
    public function __construct(private MemberService $memberService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $round = $this->stash()->get('tenant.round');
        return $this->renderView('pages.planning.member.count', [
            'count' => $this->memberService->getMemberCount($round),
        ]);
    }
}
