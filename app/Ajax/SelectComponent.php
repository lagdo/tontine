<?php

namespace App\Ajax;

use App\Ajax\Web\SidebarMenuRound;
use App\Ajax\Web\SidebarMenuTontine;
use App\Ajax\Web\TontineName;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;

use function config;

abstract class SelectComponent extends Component
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
        $this->cl(TontineName::class)->show($tontine->name);

        // Set the tontine sidebar menu
        $this->cl(SidebarMenuTontine::class)->render();
        $this->response->jq('a', '#sidebar-menu-tontine')->css('color', self::$activeMenuColor);

        foreach(config('menu.tontine') as $menuId => $menuClass)
        {
            $this->response->jq($menuId)->click($this->rq($menuClass)->home());
        }

        // Reset the round sidebar menu
        $this->cl(SidebarMenuRound::class)->render();
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    protected function selectRound(Round $round)
    {
        $this->cl(TontineName::class)->show($round->tontine->name . ' - ' . $round->title);

        // Set the round sidebar menu
        $this->cl(SidebarMenuRound::class)->render();
        $this->response->jq('a', '#sidebar-menu-round')->css('color', self::$activeMenuColor);

        foreach(config('menu.round') as $menuId => $menuClass)
        {
            $this->response->jq($menuId)->click($this->rq($menuClass)->home());
        }
    }
}
