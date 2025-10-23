<?php

namespace Ajax\App\Admin\Guest;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

#[Databag('admin')]
class Guild extends Component
{
    public function html(): Stringable|string
    {
        return $this->renderView('pages.admin.user.guest.guild.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(GuildPage::class)->page();
    }
}
