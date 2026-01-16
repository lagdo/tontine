<?php

namespace Ajax\App\Planning\Pool;

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
        return $this->renderTpl('pages.planning.pool.session.header', [
            'pool' => $this->stash()->get('planning.pool'),
        ]);
    }
}
