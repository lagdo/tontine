<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\MeetingComponent;
use Stringable;

class Fee extends MeetingComponent
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.charge.libre.home', [
            'session' => $this->cache()->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(FeePage::class)->page(1);
    }
}
