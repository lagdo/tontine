<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Guild\UserService;

#[Exclude]
class GuildAccess extends Component
{
    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $invite = $this->stash()->get('user.access.invite');
        $guild = $this->stash()->get('user.access.guild');

        return $this->renderTpl('pages.admin.user.host.access.guild', [
            'guild' => $guild,
            'access' => $this->userService->getHostGuildAccess($invite, $guild),
        ]);
    }
}
