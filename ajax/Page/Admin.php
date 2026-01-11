<?php

namespace Ajax\Page;

use Ajax\Base\FuncComponent;
use Ajax\Page\Header\GuildHeader;
use Ajax\Page\Sidebar\AdminMenu;

use function view;

class Admin extends FuncComponent
{
    /**
     * Show the home page.
     *
     * @return void
     */
    public function home(): void
    {
        $this->bag('tenant')->set('guild.id', 0);
        $this->bag('tenant')->set('round.id', 0);
        $this->stash()->set('tenant.guild', null);
        $this->stash()->set('tenant.round', null);

        view()->share('currentGuild', null);
        view()->share('currentRound', null);

        $this->cl(GuildHeader::class)->render();
        $this->cl(AdminMenu::class)->render();
    }
}
