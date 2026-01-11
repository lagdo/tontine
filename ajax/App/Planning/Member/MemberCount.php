<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Planning\MemberService;

#[Exclude]
class MemberCount extends Component
{
    public function __construct(private MemberService $memberService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.planning.member.count', [
            'count' => $this->memberService->getMemberCount($this->round()),
        ]);
    }
}
