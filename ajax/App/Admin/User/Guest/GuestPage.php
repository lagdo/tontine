<?php

namespace Ajax\App\Admin\User\Guest;

use Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\UserService;
use Stringable;

/**
 * @databag user
 */
class GuestPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['user', 'guest.page'];

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
        return $this->userService->getGuestInviteCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.user.guest.page', [
            'invites' => $this->userService->getGuestInvites($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-guest-invites-page');
    }
}
