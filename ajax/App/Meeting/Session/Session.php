<?php

namespace Ajax\App\Meeting\Session;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

use function trans;

#[Before('checkHostAccess', ["meeting", "sessions"])]
#[Databag('meeting')]
class Session extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Before('checkRoundSessions')]
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.meeting'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SessionPage::class)->page();
    }
}
