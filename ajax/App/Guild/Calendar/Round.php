<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Base\Guild\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;

#[Before('checkHostAccess', ["guild", "calendar"])]
#[Databag('guild.calendar')]
#[Export(base: ['render'])]
class Round extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["guild", "calendar"])]
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
        return $this->renderTpl('pages.guild.calendar.round.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(RoundPage::class)->page();
    }
}
