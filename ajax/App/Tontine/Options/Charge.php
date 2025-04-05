<?php

namespace Ajax\App\Tontine\Options;

use Ajax\Component;
use Stringable;

/**
 * @databag charge
 */
class Charge extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.tontine.options.charge.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(ChargePage::class)->page();
    }
}
