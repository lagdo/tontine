<?php

namespace Ajax\App\Admin\Guest;

use Ajax\Component;
use Stringable;

/**
 * @databag admin
 */
class Guild extends Component
{
    public function html(): Stringable|string
    {
        return $this->renderView('pages.admin.user.guest.guild.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(GuildPage::class)->page();
    }
}
