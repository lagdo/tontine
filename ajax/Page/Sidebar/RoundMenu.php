<?php

namespace Ajax\Page\Sidebar;

use Ajax\App\Planning\Enrollment;
use Ajax\Base\Component;
use Ajax\Page\Header\RoundMenuFunc;
use Jaxon\Attributes\Attribute\Exclude;

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
    public function html(): string
    {
        return $this->renderTpl('parts.sidebar.round');
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

        $this->cl(Enrollment::class)->home();
        $this->setSectionTitle('planning', 'enrollment');

        $back = $this->renderTpl('parts.sidebar.back', [
            'handler' => $this->rq(RoundMenuFunc::class)->back(),
        ]);
        $this->response->html('header-menu-back', $back);
        $this->response->jq('#header-menu-back')->show();
    }
}
