<?php

namespace App\Ajax\Web\Tontine\Guest\Invite;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\GuestService;

/**
 * @databag invite
 */
class HostPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['invite', 'host.page'];

    /**
     * @param GuestService $guestService
     */
    public function __construct(private GuestService $guestService)
    {}

    public function html(): string
    {
        return $this->renderView('pages.invite.host.page', [
            'invites' => $this->guestService->getHostInvites($this->page),
        ]);
    }

    protected function count(): int
    {
        return $this->guestService->getHostInviteCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('content-host-invites-page');

        return $this->response;
    }
}
