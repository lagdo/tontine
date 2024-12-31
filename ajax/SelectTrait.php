<?php

namespace Ajax;

use Ajax\App\MainTitle;
use Ajax\App\Sidebar\RoundMenu;
use Ajax\App\Sidebar\TontineMenu;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;

trait SelectTrait
{
    /**
     * @param Tontine $tontine
     *
     * @return void
     */
    protected function selectTontine(Tontine $tontine)
    {
        $this->stash()->set('menu.current.tontine', $tontine);
        $this->stash()->set('menu.current.round', null);
        $this->cl(MainTitle::class)->render();

        // Set the tontine sidebar menu
        $this->stash()->set('menu.tontine.active', true);
        $this->cl(TontineMenu::class)->render();

        // Reset the round sidebar menu
        $this->stash()->set('menu.round.active', false);
        $this->cl(RoundMenu::class)->render();
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    protected function selectRound(Round $round)
    {
        $this->stash()->set('menu.current.tontine', $round->tontine);
        $this->stash()->set('menu.current.round', $round);
        $this->cl(MainTitle::class)->render();

        // Set the round sidebar menu
        $this->stash()->set('menu.round.active', true);
        $this->cl(RoundMenu::class)->render();
    }
}
