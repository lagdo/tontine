<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Base\PageComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\UserService;

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
    public function html(): string
    {
        $user = $this->tenantService->user();
        return $this->renderTpl('pages.admin.user.host.page', [
            'invites' => $this->userService->getHostInvites($user, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-host-invites-page');
    }
}
