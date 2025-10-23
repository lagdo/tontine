<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
class SessionHeader extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.fund.session.header', [
            'fund' => $this->stash()->get('planning.fund'),
        ]);
    }
}
