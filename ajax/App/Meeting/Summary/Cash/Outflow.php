<?php

namespace Ajax\App\Meeting\Summary\Cash;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

class Outflow extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.outflow.home', [
            'session' => $this->stash()->get('summary.session'),
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
