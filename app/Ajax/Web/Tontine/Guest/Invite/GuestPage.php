<?php

namespace App\Ajax\Web\Tontine\Guest\Invite;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\GuestService;

/**
 * @databag invite
 */
class GuestPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['invite', 'guest.page'];

    /**
     * @param GuestService $guestService
     */
    public function __construct(private GuestService $guestService)
    {}

    public function html(): string
    {
        return $this->renderView('pages.invite.guest.page', [
            'invites' => $this->guestService->getGuestInvites($this->page),
        ]);
    }

    protected function count(): int
    {
        return $this->guestService->getGuestInviteCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('content-guest-invites-page');

        return $this->response;
    }
}
