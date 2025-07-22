<?php

namespace Ajax\App;

use Siak\Tontine\Service\Guild\MemberService;
use Ajax\FuncComponent;

use function intval;

/**
 * @databag faker
 */
class FakerFunc extends FuncComponent
{
    /**
     * @param MemberService $memberService
     */
    public function __construct(private MemberService $memberService)
    {}

    public function members(): void
    {
        $guild = $this->stash()->get('tenant.guild');
        $count = intval($this->bag('faker')->get('member.count', 10));
        $html = $this->memberService
            ->getFakeMembers($guild, $count)
            ->map(fn($member) => "{$member->name};{$member->email}")
            ->join("\n");
        $this->response->html('new-members-list', $html);
    }
}
