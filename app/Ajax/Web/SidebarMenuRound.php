<?php

namespace App\Ajax\Web;

use App\Ajax\Component;

/**
 * @exclude
 */
class SidebarMenuRound extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('parts.sidebar.round');
    }
}
