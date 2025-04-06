<?php

namespace Ajax\App\Guild\Options;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Stringable;

use function trans;

class Options extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkHostAccess ["tontine", "categories"]
     * @databag charge
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontine'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.options.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->setSmScreenHandler('options-sm-screens');

        $this->cl(Fund::class)->render();
        $this->cl(Category::class)->render();
        $this->cl(Charge::class)->render();
    }
}
