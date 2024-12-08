<?php

namespace Ajax\App;

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
        return $this->renderView('pages.select.title', [
            'tontine' => $this->cache->get('menu.current.tontine'),
            'round' => $this->cache->get('menu.current.round'),
        ]);
    }
}
