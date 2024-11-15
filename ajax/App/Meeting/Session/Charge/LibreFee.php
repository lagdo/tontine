<?php

namespace Ajax\App\Meeting\Session\Charge;

use Ajax\App\Meeting\MeetingComponent;

class LibreFee extends MeetingComponent
{
    public function html(): string
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
