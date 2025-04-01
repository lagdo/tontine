<?php

namespace Ajax\App\Page;

use Ajax\Component;

/**
 * @exclude
 */
class MainTitle extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('parts.header.title');
    }
}
