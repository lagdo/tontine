<?php

namespace Ajax\App\Tontine\Invite;

use Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\InviteService;
use Stringable;

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
    public function html(): Stringable
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
