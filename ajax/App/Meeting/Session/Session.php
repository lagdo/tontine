<?php

namespace Ajax\App\Meeting\Session;

use Ajax\Base\Round\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;

#[Before('checkHostAccess', ["meeting", "sessions"])]
#[Databag('meeting')]
class Session extends Component
{
    /**
     * @var string
     */
    protected string $overrides = SectionContent::class;

    #[Before('checkRoundSessions')]
    #[Before('setSectionTitle', ["meeting", "sessions"])]
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
        return $this->renderTpl('pages.meeting.session.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SessionPage::class)->page();
    }
}
