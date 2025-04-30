<?php

namespace Ajax\App\Meeting\Summary\Cash;

use Ajax\App\Meeting\Summary\Component;
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
    protected function after()
    {
        $this->cl(OutflowPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
