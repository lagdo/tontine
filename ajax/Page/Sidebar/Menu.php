<?php

namespace Ajax\Page\Sidebar;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
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
