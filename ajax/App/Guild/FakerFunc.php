<?php

namespace Ajax\App\Guild;

use Ajax\Base\Guild\FuncComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\MemberService;

use function intval;

#[Databag('faker')]
class FakerFunc extends FuncComponent
{
    /**
     * @param MemberService $memberService
     */
    public function __construct(private MemberService $memberService)
    {}

    public function members(): void
    {
        $count = intval($this->bag('faker')->get('member.count', 10));
        $html = $this->memberService
            ->getFakeMembers($this->guild(), $count)
            ->map(fn($member) => "{$member->name};{$member->email}")
            ->join("\n");
        $this->response->html('new-members-list', $html);
    }
}
