<?php

namespace Ajax\App\Guild\Pool;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

use function trans;

#[Before('checkHostAccess', ["finance", "pools"])]
#[Databag('guild.pool')]
class Pool extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Callback('jaxon.ajax.callback.hideMenuOnMobile')]
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
        return $this->renderView('pages.guild.pool.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(PoolPage::class)->page();
    }
}
