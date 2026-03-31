<?php

namespace Ajax\App\Guild\Member;

use Ajax\Base\Guild\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\MemberService;

#[Before('checkHostAccess', ["guild", "members"])]
#[Databag('guild.member')]
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
        $search = $this->bag('guild.member')->get('search', '');
        $filter = $this->bag('guild.member')->get('filter', null);

        return $this->memberService->getMemberCount($this->guild(), $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = $this->bag('guild.member')->get('search', '');
        $filter = $this->bag('guild.member')->get('filter', null);

        return $this->renderTpl('pages.guild.member.page', [
            'members' => $this->memberService
                ->getMembers($this->guild(), $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-member-page');
    }
}
