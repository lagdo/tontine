<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Component;
use Stringable;

class Fee extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.charge.libre.home', [
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
