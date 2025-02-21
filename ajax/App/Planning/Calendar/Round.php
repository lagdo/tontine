<?php

namespace Ajax\App\Planning\Calendar;

use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Ajax\Component;
use Stringable;

use function trans;

/**
 * @databag planning
 */
class Round extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkHostAccess ["planning", "sessions"]
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
        return $this->renderView('pages.planning.calendar.round.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(RoundPage::class)->page();
    }
}
