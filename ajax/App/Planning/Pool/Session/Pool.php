<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\App\Planning\Pool\Session\Pool\StartSession;
use Ajax\App\SectionContent;
use Ajax\Component;
use Stringable;

/**
 * @databag pool
 * @databag pool.session
 */
class Pool extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->page();

        $pools = $this->cache()->get('pool.session.pools');
        if($pools !== null && $pools->count() > 0)
        {
            $pool = $pools[0];
            $this->bag('pool.session')->set('pool.id', $pool->id);
            // Show the start session page of the first pool in the list.
            $this->cache()->set('pool.session.pool', $pool);
            $this->cl(StartSession::class)->pool($pool->id);
        }
    }
}
