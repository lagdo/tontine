<?php

namespace App\Ajax\Web\Tontine\Invite;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\InviteService;

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
     * @param InviteService $inviteService
     */
    public function __construct(private InviteService $inviteService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->inviteService->getHostInviteCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.invite.host.page', [
            'invites' => $this->inviteService->getHostInvites($this->page),
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
