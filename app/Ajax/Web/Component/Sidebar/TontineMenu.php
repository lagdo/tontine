<?php

namespace App\Ajax\Web\Component\Sidebar;

use App\Ajax\Component;

/**
 * @exclude
 */
class TontineMenu extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('parts.sidebar.tontine');
    }
}
