<?php

namespace Ajax\App\Admin\User\Guest;

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
        return $this->renderView('pages.user.guest.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(GuestPage::class)->page();
    }
}
