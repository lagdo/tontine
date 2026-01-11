<?php

namespace Ajax\Page\Sidebar;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

use function config;

#[Exclude]
class RoundMenu extends Component
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
        return $this->renderView('parts.sidebar.round');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->node()->jq('.sidebar-menu a')->css('color', config('menu.color.active'));
        foreach(config('menu.round') as $menuId => $menuClass)
        {
            $this->node()->jq($menuId)->click($this->rq($menuClass)->home());
        }
    }
}
