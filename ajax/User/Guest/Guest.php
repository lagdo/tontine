<?php

namespace Ajax\User\Guest;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Databag('user')]
#[Export(base: ['render'])]
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
