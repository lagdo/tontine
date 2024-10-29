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

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->guestService->getGuestInviteCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.invite.guest.page', [
            'invites' => $this->guestService->getGuestInvites($this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-guest-invites-page');
    }
}
