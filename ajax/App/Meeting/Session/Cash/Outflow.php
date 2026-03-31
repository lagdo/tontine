<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Exclude;

class Outflow extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.outflow.home', [
            'session' => $this->stash()->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(OutflowPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    #[Exclude]
    public function show(): void
    {
        $this->render();
    }
}
