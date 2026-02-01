<?php

namespace Ajax\Page;

use Ajax\Base\FuncComponent;
use Ajax\Page\Header\GuildHeader;
use Ajax\Page\Header\GuildMenuFunc;
use Ajax\Page\Header\RoundMenuFunc;
use Ajax\Page\Sidebar\AdminMenu;
use Siak\Tontine\Model\Guild as GuildModel;
use Siak\Tontine\Model\Round as RoundModel;

use function view;

class Admin extends FuncComponent
{
    /**
     * @param GuildModel|null $guild
     *
     * @return void
     */
    private function setCurrentGuild(GuildModel|null $guild): void
    {
        $this->bag('tenant')->set('guild.id', $guild?->id ?? 0);
        $this->stash()->set('tenant.guild', $guild);
        view()->share('currentGuild', $guild);
    }

    /**
     * @param RoundModel|null $round
     *
     * @return void
     */
    private function setCurrentRound(RoundModel|null $round): void
    {
        $this->bag('tenant')->set('round.id', $round?->id ?? 0);
        $this->stash()->set('tenant.round', $round);
        view()->share('currentRound', $round);
    }

    /**
     * Show the home page.
     *
     * @return void
     */
    public function home(): void
    {
        if(($guild = $this->tenantService->getLatestGuild()) === null)
        {
            $this->setCurrentGuild(null);
            $this->setCurrentRound(null);
            // Go to the Administration page
            $this->cl(GuildHeader::class)->render();
            $this->cl(AdminMenu::class)->render();
            return;
        }

        $this->setCurrentGuild($guild);

        $this->tenantService->setGuild($guild);
        if(($round = $this->tenantService->getLatestRound()) === null)
        {
            $this->setCurrentRound(null);
            // Go to the latest Guild page
            $this->cl(GuildMenuFunc::class)->setCurrentGuild($guild);
            return;
        }

        $this->setCurrentRound($round);
        // Go to the latest Round page
        $this->cl(RoundMenuFunc::class)->setCurrentRound($round);
    }
}
