<?php

namespace Ajax\App\Tontine\Member;

use Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\MemberService;
use Stringable;

use function trim;

/**
 * @databag member
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
        $search = trim($this->bag('member')->get('search', ''));

        return $this->renderView('pages.tontine.member.page', [
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
        $search = trim($this->bag('member')->get('search', ''));

        return $this->memberService->getMemberCount($search);
    }
}
