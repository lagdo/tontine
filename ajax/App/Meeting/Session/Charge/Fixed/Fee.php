<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Component;
use Stringable;

class Fee extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.charge.fixed.home', [
            'session' => $this->stash()->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(FeePage::class)->page(1);
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
