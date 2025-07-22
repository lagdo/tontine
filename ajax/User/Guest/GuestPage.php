<?php

namespace Ajax\User\Guest;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\UserService;
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
        $user = $this->tenantService->user();
        return $this->userService->getGuestInviteCount($user);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $user = $this->tenantService->user();
        return $this->renderView('pages.admin.user.guest.page', [
            'invites' => $this->userService->getGuestInvites($user, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-guest-invites-page');
    }
}
