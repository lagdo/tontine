<?php

namespace Ajax\App\Meeting\Session\Charge;

use Ajax\App\Meeting\MeetingComponent;
use Stringable;

class LibreFee extends MeetingComponent
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.charge.libre.home', [
            'session' => $this->cache->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(LibreFeePage::class)->page(1);
    }
}
