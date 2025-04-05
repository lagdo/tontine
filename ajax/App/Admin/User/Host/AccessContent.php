<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Component;
use Siak\Tontine\Service\Tontine\UserService;
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
        $tontine = $this->stash()->get('user.tontine');

        return $this->renderView('pages.admin.user.host.access.tontine', [
            'tontine' => $tontine,
            'access' => $this->userService->getHostTontineAccess($invite, $tontine),
        ]);
    }
}
