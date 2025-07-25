<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag guild.calendar
 * @before checkHostAccess ["guild", "calendar"]
 */
class Round extends Component
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
