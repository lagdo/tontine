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
        $invite = $this->cache()->get('user.invite');
        $tontine = $this->cache()->get('user.tontine');

        return $this->renderView('pages.user.host.access.tontine', [
            'tontine' => $tontine,
            'access' => $this->userService->getHostTontineAccess($invite, $tontine),
        ]);
    }
}
