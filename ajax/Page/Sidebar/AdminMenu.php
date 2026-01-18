<?php

namespace Ajax\Page\Sidebar;

use Ajax\App\Admin\Guild\Guild;
use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Exclude;

use function config;

#[Exclude]
class AdminMenu extends Component
{
    /**
     * @var string
     */
    protected $overrides = Menu::class;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('parts.sidebar.admin', ['ajax' => true]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->node()->jq('#admin-menu a')->css('color', config('menu.color.active'));
        foreach(config('menu.admin') as $menuId => $menuClass)
        {
            $this->node()->jq($menuId)->click($this->rq($menuClass)->home());
        }

        $this->cl(Guild::class)->home();
        $this->setSectionTitle('admin', 'guilds');

        $this->response()->html('header-menu-back', '');
        $this->response()->jq('#header-menu-back')->hide();
    }
}
