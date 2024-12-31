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
            'tontine' => $this->stash()->get('menu.current.tontine'),
            'round' => $this->stash()->get('menu.current.round'),
        ]);
    }
}
