<?php

namespace Ajax\App\Admin\Guest;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Databag('admin')]
#[Export(base: ['render'])]
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
