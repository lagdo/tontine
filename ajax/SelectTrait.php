<?php

namespace Ajax;

use Ajax\App\Organisation;
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
        $this->cache->set('menu.tontine.name', $tontine->name);
        $this->cl(Organisation::class)->render();

        // Set the tontine sidebar menu
        $this->cache->set('menu.tontine.active', true);
        $this->cl(TontineMenu::class)->render();

        // Reset the round sidebar menu
        $this->cache->set('menu.round.active', false);
        $this->cl(RoundMenu::class)->render();
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    protected function selectRound(Round $round)
    {
        $this->cache->set('menu.tontine.name', $round->tontine->name . ' - ' . $round->title);
        $this->cl(Organisation::class)->render();

        // Set the round sidebar menu
        $this->cache->set('menu.round.active', true);
        $this->cl(RoundMenu::class)->render();
    }
}
