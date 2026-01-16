<?php

namespace Ajax\App\Guild\Charge;

use Ajax\Base\Guild\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;

#[Before('checkHostAccess', ["finance", "charges"])]
#[Databag('guild.charge')]
#[Export(base: ['render'])]
class Charge extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["finance", "charges"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.guild.charge.home');
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
