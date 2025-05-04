<?php

namespace Ajax\Page;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class MainTitle extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('parts.header.title');
    }
}
