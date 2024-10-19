<?php

namespace App\Ajax\Web;

use App\Ajax\Component;

/**
 * @exclude
 */
class SidebarMenuTontine extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('parts.sidebar.tontine');
    }
}
