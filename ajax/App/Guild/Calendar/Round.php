<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

use function trans;

#[Before('checkHostAccess', ["guild", "calendar"])]
#[Databag('guild.calendar')]
class Round extends Component
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontine'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.calendar.round.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(RoundPage::class)->page();
    }
}
