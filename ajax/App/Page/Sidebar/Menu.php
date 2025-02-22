<?php

namespace Ajax\App\Page\Sidebar;

use Ajax\Component;

/**
 * @exclude
 */
class Menu extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('parts.sidebar.admin', ['ajax' => false]);
    }
}
