<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Guild\MemberService as GuildMemberService;
use Siak\Tontine\Service\Planning\MemberService;

#[Exclude]
class MemberCount extends Component
{
    public function __construct(private MemberService $memberService,
        private GuildMemberService $guildMemberService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.member.count', [
            'count' => $this->memberService->getMemberCount($this->round()),
            'total' => $this->guildMemberService->getMemberCount($this->guild()),
        ]);
    }
}
