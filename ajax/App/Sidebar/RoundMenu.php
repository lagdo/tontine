<?php

namespace Ajax\App\Sidebar;

use Ajax\Component;

/**
 * @exclude
 */
class RoundMenu extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('parts.sidebar.round');
    }
}
