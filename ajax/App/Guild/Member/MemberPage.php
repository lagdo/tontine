<?php

namespace Ajax\App\Guild\Member;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\MemberService;
use Stringable;

/**
 * @databag guild.member
 * @before checkHostAccess ["guild", "members"]
 */
class MemberPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['guild.member', 'page'];

    /**
     * @param MemberService $memberService
     */
    public function __construct(private MemberService $memberService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $guild = $this->stash()->get('tenant.guild');
        $search = $this->bag('guild.member')->get('search', '');
        $filter = $this->bag('guild.member')->get('filter', null);

        return $this->memberService->getMemberCount($guild, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guild = $this->stash()->get('tenant.guild');
        $search = $this->bag('guild.member')->get('search', '');
        $filter = $this->bag('guild.member')->get('filter', null);

        return $this->renderView('pages.guild.member.page', [
            'members' => $this->memberService
                ->getMembers($guild, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-member-page');
    }
}
