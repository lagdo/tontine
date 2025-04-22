<?php

namespace Ajax\App\Page\Sidebar;

use Ajax\Component;
use Stringable;

use function config;

/**
 * @exclude
 */
class AdminMenu extends Component
{
    /**
     * @var string
     */
    protected $overrides = Menu::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('parts.sidebar.admin', ['ajax' => true]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->node()->jq('#admin-menu a')->css('color', config('menu.color.active'));
        foreach(config('menu.admin') as $menuId => $menuClass)
        {
            $this->node()->jq($menuId)->click($this->rq($menuClass)->home());
        }

        if($this->stash()->get('menu.current.guild') !== null)
        {
            $this->node()->jq('#finance-menu a')->css('color', config('menu.color.active'));
            foreach(config('menu.finance') as $menuId => $menuClass)
            {
                $this->node()->jq($menuId)->click($this->rq($menuClass)->home());
            }
            $this->node()->jq('#tontine-menu a')->css('color', config('menu.color.active'));
            foreach(config('menu.tontine') as $menuId => $menuClass)
            {
                $this->node()->jq($menuId)->click($this->rq($menuClass)->home());
            }
        }
    }
}
