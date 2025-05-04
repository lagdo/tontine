<?php

namespace Ajax\App\Guild\Member;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\MemberService;
use Stringable;

/**
 * @databag member
 * @before checkHostAccess ["guild", "members"]
 */
class MemberPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['member', 'page'];

    /**
     * @param MemberService $memberService
     */
    public function __construct(private MemberService $memberService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = $this->bag('member')->get('search', '');

        return $this->renderView('pages.guild.member.page', [
            'members' => $this->memberService->getMembers($search, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-member-page');
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('member')->get('search', '');

        return $this->memberService->getMemberCount($search);
    }
}
