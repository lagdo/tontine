<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\PageComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Planning\MemberService;

#[Databag('planning.member')]
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
        $search = $this->bag('planning.member')->get('search', '');
        $filter = $this->bag('planning.member')->get('filter', null);
        return $this->memberService->getMemberDefCount($this->round(), $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = $this->bag('planning.member')->get('search', '');
        $filter = $this->bag('planning.member')->get('filter', null);
        return $this->renderTpl('pages.planning.member.page', [
            'round' => $this->round(),
            'defs' => $this->memberService->getMemberDefs($this->round(), $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-planning-member-page');
    }
}
