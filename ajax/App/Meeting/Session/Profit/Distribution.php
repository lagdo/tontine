<?php

namespace Ajax\App\Meeting\Session\Profit;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class Distribution extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.profit.distribution', [
            'distribution' => $this->stash()->get('profit.savings.distribution'),
        ]);
    }
}
