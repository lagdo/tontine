<?php

namespace App\Ajax\Web\Tontine\Member;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\MemberService;

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
    public function html(): string
    {
        $search = trim($this->bag('member')->get('search', ''));

        return $this->renderView('pages.member.page', [
            'members' => $this->memberService->getMembers($search, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
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
