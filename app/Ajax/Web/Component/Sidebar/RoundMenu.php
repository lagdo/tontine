<?php

namespace App\Ajax\Web\Component\Sidebar;

use App\Ajax\Component;

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
