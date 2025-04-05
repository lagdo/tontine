<?php

namespace Ajax\App\Admin\User\Guest;

use Ajax\Component;
use Stringable;

/**
 * @databag user
 */
class Organisation extends Component
{
    public function html(): Stringable|string
    {
        return $this->renderView('pages.admin.user.guest.organisation.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(OrganisationPage::class)->page();
    }
}
