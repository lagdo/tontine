<?php

namespace App\Ajax;

use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;

use function config;

class SelectCallable extends CallableClass
{
    /**
     * @var string
     */
    static protected $activeMenuColor = '#6777ef';

    /**
     * @param Tontine $tontine
     *
     * @return void
     */
    protected function selectTontine(Tontine $tontine)
    {
        $this->response->html('section-tontine-name', $tontine->name);

        // Set the tontine sidebar menu
        $this->response->html('sidebar-menu-tontine', $this->renderView('parts.sidebar.tontine'));
        $this->response->jq('a', '#sidebar-menu-tontine')->css('color', self::$activeMenuColor);

        foreach(config('menu.tontine') as $menuId => $menuClass)
        {
            $this->response->jq($menuId)->click($this->rq($menuClass)->home());
        }

        // Reset the round sidebar menu
        $this->response->html('sidebar-menu-round', $this->renderView('parts.sidebar.round'));
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
        $this->response->html('sidebar-menu-round', $this->renderView('parts.sidebar.round'));
        $this->response->jq('a', '#sidebar-menu-round')->css('color', self::$activeMenuColor);

        foreach(config('menu.round') as $menuId => $menuClass)
        {
            $this->response->jq($menuId)->click($this->rq($menuClass)->home());
        }
    }
}
