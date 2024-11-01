<?php

namespace App\Ajax\Web\Tontine\Invite;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\InviteService;

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
     * @param InviteService $inviteService
     */
    public function __construct(private InviteService $inviteService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->inviteService->getGuestInviteCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.invite.guest.page', [
            'invites' => $this->inviteService->getGuestInvites($this->page),
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
