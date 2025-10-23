<?php

namespace Ajax\User\Guest;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

#[Databag('user')]
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
    protected function after(): void
    {
        $this->cl(GuestPage::class)->page();
    }
}
