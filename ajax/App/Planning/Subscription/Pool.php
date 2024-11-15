<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;

/**
 * @databag pool
 */
class Pool extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.planning.subscription.pool.home', [
            'rqPool' => $this->rq(),
            'rqPoolPage' => $this->rq(PoolPage::class),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->page();
    }
}
