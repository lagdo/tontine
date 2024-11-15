<?php

namespace Ajax\App;

use Ajax\Component;

/**
 * @exclude
 */
class Organisation extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->cache->get('menu.tontine.name');
    }
}
