<?php

namespace App\Ajax;

use App\Ajax\Web\Component\Organisation;
use App\Ajax\Web\Component\Sidebar\RoundMenu;
use App\Ajax\Web\Component\Sidebar\TontineMenu;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;

use function config;

trait SelectTrait
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
        $this->cl(Organisation::class)->show($tontine->name);
        // Set the tontine sidebar menu
        $this->cl(TontineMenu::class)->render();
        $this->response->jq('a', '#sidebar-menu-tontine')->css('color', self::$activeMenuColor);

        foreach(config('menu.tontine') as $menuId => $menuClass)
        {
            $this->response->jq($menuId)->click($this->rq($menuClass)->home());
        }

        // Reset the round sidebar menu
        $this->cl(RoundMenu::class)->render();
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    protected function selectRound(Round $round)
    {
        $this->cl(Organisation::class)->show($round->tontine->name . ' - ' . $round->title);

        // Set the round sidebar menu
        $this->cl(RoundMenu::class)->render();
        $this->response->jq('a', '#sidebar-menu-round')->css('color', self::$activeMenuColor);

        foreach(config('menu.round') as $menuId => $menuClass)
        {
            $this->response->jq($menuId)->click($this->rq($menuClass)->home());
        }
    }
}
