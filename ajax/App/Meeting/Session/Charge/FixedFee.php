<?php

namespace Ajax\App\Meeting\Session\Charge;

use Ajax\App\Meeting\MeetingComponent;
use Stringable;

class FixedFee extends MeetingComponent
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.charge.fixed.home', [
            'session' => $this->cache->get('meeting.session'),
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
