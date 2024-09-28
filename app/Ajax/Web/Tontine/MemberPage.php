<?php

namespace App\Ajax\Web\Tontine;

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

    public function html(): string
    {
        $search = trim($this->bag('member')->get('search', ''));

        return $this->renderView('pages.member.page', [
            'members' => $this->memberService->getMembers($search, $this->page),
        ]);
    }

    protected function count(): int
    {
        $search = trim($this->bag('member')->get('search', ''));

        return $this->memberService->getMemberCount($search);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('content-page');

        return $this->response;
    }
}
