<?php

namespace Ajax\App\Sidebar;

use Ajax\Component;
use Stringable;

use function config;

/**
 * @exclude
 */
class RoundMenu extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('parts.sidebar.round');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        if($this->stash()->get('menu.round.active'))
        {
            $this->response->jq('a', '#sidebar-menu-round')
                ->css('color', config('menu.color.active'));
            foreach(config('menu.round') as $menuId => $menuClass)
            {
                $this->response->jq($menuId)->click($this->rq($menuClass)->home());
            }
        }
    }
}
