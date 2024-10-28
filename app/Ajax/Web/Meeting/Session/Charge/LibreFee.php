<?php

namespace App\Ajax\Web\Meeting\Session\Charge;

use App\Ajax\Web\Meeting\MeetingComponent;

class LibreFee extends MeetingComponent
{
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.charge.libre.home', [
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
