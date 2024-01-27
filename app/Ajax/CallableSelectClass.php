<?php

namespace App\Ajax;

use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;

use function config;

class CallableSelectClass extends CallableClass
{
    /**
     * @param Tontine $tontine
     *
     * @return void
     */
    protected function selectTontine(Tontine $tontine)
    {
        $this->response->html('section-tontine-name', $tontine->name);

        // Set the tontine sidebar menu
        $this->response->html('sidebar-menu-tontine', $this->render('parts.sidebar.tontine'));
        $this->jq('a', '#sidebar-menu-tontine')->css('color', '#6777ef');

        foreach(config('menu.tontine') as $menuId => $menuClass)
        {
            $this->jq($menuId)->click($this->rq($menuClass)->home());
        }

        // Reset the round sidebar menu
        $this->response->html('sidebar-menu-round', $this->render('parts.sidebar.round'));
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    protected function selectRound(Round $round)
    {
        $this->response->html('section-tontine-name', $round->tontine->name . ' - ' . $round->title);

        // Set the round sidebar menu
        $this->response->html('sidebar-menu-round', $this->render('parts.sidebar.round'));
        $this->jq('a', '#sidebar-menu-round')->css('color', '#6777ef');

        foreach(config('menu.round') as $menuId => $menuClass)
        {
            $this->jq($menuId)->click($this->rq($menuClass)->home());
        }
    }
}
