<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\Component;
use Stringable;

class Outflow extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.outflow.home', [
            'session' => $this->stash()->get('meeting.session'),
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
