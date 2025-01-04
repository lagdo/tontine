<?php

namespace Ajax\App\Page\Sidebar;

use Ajax\Component;
use Stringable;

use function config;

/**
 * @exclude
 */
class TontineMenu extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('parts.sidebar.tontine');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        if($this->stash()->get('menu.tontine.active'))
        {
            $this->response->jq('a', '#sidebar-menu-tontine')
                ->css('color', config('menu.color.active'));
            foreach(config('menu.tontine') as $menuId => $menuClass)
            {
                $this->response->jq($menuId)->click($this->rq($menuClass)->home());
            }
        }
    }
}
