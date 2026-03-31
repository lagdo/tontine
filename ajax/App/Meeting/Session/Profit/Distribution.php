<?php

namespace Ajax\App\Meeting\Session\Profit;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class Distribution extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.profit.distribution', [
            'distribution' => $this->stash()->get('profit.savings.distribution'),
        ]);
    }
}
