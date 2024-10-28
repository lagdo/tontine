<?php

namespace App\Ajax\Web\Meeting\Session\Charge;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingComponent;

class FixedFee extends MeetingComponent
{
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.charge.fixed.home', [
            'session' => Cache::get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(FixedFeePage::class)->page(1);
    }
}
