<?php

namespace Ajax\Page\Sidebar;

use Ajax\App\Guild\Member\Member;
use Ajax\Base\Component;
use Ajax\Page\Header\GuildMenuFunc;
use Jaxon\Attributes\Attribute\Exclude;

use function config;

#[Exclude]
class GuildMenu extends Component
{
    /**
     * @var string
     */
    protected string $overrides = Menu::class;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('parts.sidebar.guild');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->node()->jq('.sidebar-menu a')->css('color', config('menu.color.active'));
        foreach(config('menu.guild') as $menuId => $menuClass)
        {
            $this->node()->jq($menuId)->click($this->rq($menuClass)->home());
        }

        $this->cl(Member::class)->home();
        $this->setSectionTitle('guild', 'members');

        $back = $this->renderTpl('parts.sidebar.back', [
            'handler' => $this->rq(GuildMenuFunc::class)->back(),
        ]);
        $this->response()->html('header-menu-back', $back);
        $this->response()->jq('#header-menu-back')->show();
    }
}
