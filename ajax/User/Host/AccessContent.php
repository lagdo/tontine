<?php

namespace Ajax\User\Host;

use Ajax\Component;
use Siak\Tontine\Service\Guild\UserService;
use Stringable;

/**
 * @exclude
 */
class AccessContent extends Component
{
    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $invite = $this->stash()->get('user.invite');
        $guild = $this->stash()->get('user.guild');

        return $this->renderView('pages.admin.user.host.access.guild', [
            'guild' => $guild,
            'access' => $this->userService->getHostGuildAccess($invite, $guild),
        ]);
    }
}
