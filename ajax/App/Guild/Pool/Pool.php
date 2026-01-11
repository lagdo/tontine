<?php

namespace Ajax\App\Guild\Pool;

use Ajax\Base\Guild\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Before('checkHostAccess', ["finance", "pools"])]
#[Databag('guild.pool')]
#[Export(base: ['render'])]
class Pool extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["finance", "pools"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
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
