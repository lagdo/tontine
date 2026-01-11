<?php

namespace Ajax\App\Meeting\Session\Profit;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
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
