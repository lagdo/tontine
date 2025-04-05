<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\UserService;
use Stringable;

/**
 * @databag user
 */
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
        return $this->userService->getHostInviteCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.user.host.page', [
            'invites' => $this->userService->getHostInvites($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-host-invites-page');
    }
}
