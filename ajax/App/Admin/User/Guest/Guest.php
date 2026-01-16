<?php

namespace Ajax\App\Admin\User\Guest;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;

#[Databag('user')]
#[Export(base: ['render'])]
class Guest extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.admin.user.guest.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(GuestPage::class)->page();
    }
}
