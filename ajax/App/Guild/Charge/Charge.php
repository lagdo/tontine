<?php

namespace Ajax\App\Guild\Charge;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag guild.charge
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
    protected function before(): void
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
    protected function after(): void
    {
        $this->cl(ChargePage::class)->page();
    }

    public function toggleFilter()
    {
        $filter = $this->bag('guild.charge')->get('filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('guild.charge')->set('filter', $filter);
        $this->bag('guild.charge')->set('page', 1);

        $this->cl(ChargePage::class)->page();
    }
}
