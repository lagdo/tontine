<?php

namespace App\Ajax\Web\Tontine\Invite;

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

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->guestService->getHostInviteCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.invite.host.page', [
            'invites' => $this->guestService->getHostInvites($this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-host-invites-page');
    }
}
