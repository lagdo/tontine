<?php

namespace Ajax\User;

use Ajax\Component;
use Stringable;

/**
 * @databag user
 */
class User extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.user.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Host\Host::class)->render();
        $this->cl(Guest\Guest::class)->render();

        $this->response->js('Tontine')
            ->setSmScreenHandler('invites-sm-screens', 'invites-content');
    }
}
