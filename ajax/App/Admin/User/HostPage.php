<?php

namespace Ajax\App\Admin\User;

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
        return $this->renderView('pages.user.host.page', [
            'invites' => $this->userService->getHostInvites($this->page),
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
