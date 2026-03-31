<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;

#[Before('checkHostAccess', ["report", "session"])]
#[Before('checkOpenedSessions')]
class Session extends Component
{
    /**
     * @var string
     */
    protected string $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["report", "session"])]
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
        return $this->renderTpl('pages.report.session.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SessionFunc::class)->showTables();
    }
}
