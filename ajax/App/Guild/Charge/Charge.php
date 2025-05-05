<?php

namespace Ajax\App\Guild\Charge;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag charge
 * @before checkHostAccess ["finance", "charges"]
 */
class Charge extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @callback jaxon.ajax.callback.hideMenuOnMobile
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.finance'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.charge.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(ChargePage::class)->page();
    }
}
