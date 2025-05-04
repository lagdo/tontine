<?php

namespace Ajax\User\Guest;

use Ajax\Component;
use Stringable;

/**
 * @databag user
 */
class Guest extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.user.guest.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(GuestPage::class)->page();
    }
}
