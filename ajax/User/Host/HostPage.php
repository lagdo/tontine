<?php

namespace Ajax\User\Host;

use Ajax\PageComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\UserService;
use Stringable;

#[Databag('user')]
class HostPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['user', 'host.page'];

    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $user = $this->tenantService->user();
        return $this->userService->getHostInviteCount($user);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $user = $this->tenantService->user();
        return $this->renderView('pages.admin.user.host.page', [
            'invites' => $this->userService->getHostInvites($user, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-host-invites-page');
    }
}
