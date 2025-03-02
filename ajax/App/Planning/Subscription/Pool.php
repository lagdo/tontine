<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
use Stringable;

/**
 * @databag pool
 */
class Pool extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.subscription.pool.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->page();
    }
}
