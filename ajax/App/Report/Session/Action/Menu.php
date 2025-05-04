<?php

namespace Ajax\App\Report\Session\Action;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class Menu extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('report.session');

        return $this->renderView('pages.report.session.action.menu', [
            'sessionId' => $session->id,
        ]);
    }
}
