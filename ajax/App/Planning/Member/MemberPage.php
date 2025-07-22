<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\PageComponent;
use Siak\Tontine\Service\Planning\MemberService;
use Stringable;

/**
 * @databag planning.member
 */
class MemberPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning.member', 'page'];

    /**
     * The constructor
     *
     * @param MemberService $memberService
     */
    public function __construct(private MemberService $memberService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('tenant.round');
        $search = $this->bag('planning.member')->get('search', '');
        $filter = $this->bag('planning.member')->get('filter', null);
        return $this->memberService->getMemberDefCount($round, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('tenant.round');
        $search = $this->bag('planning.member')->get('search', '');
        $filter = $this->bag('planning.member')->get('filter', null);
        return $this->renderView('pages.planning.member.page', [
            'round' => $round,
            'defs' => $this->memberService->getMemberDefs($round, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-planning-member-page');
    }
}
