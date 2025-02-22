<?php

namespace Ajax\App\Planning\Financial;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class SessionAction extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.financial.session.actions', [
            'pool' => $this->stash()->get('planning.financial.pool'),
        ]);
    }
}
