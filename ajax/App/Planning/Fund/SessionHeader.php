<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class SessionHeader extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.fund.session.header', [
            'fund' => $this->stash()->get('planning.fund'),
        ]);
    }
}
