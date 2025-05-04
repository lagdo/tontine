<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\Component;
use Stringable;

/**
 * @databag planning.finance.pool
 * @before getPool
 */
class Session extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = Pool::class;

    public function pool(int $poolId)
    {
        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.home', [
            'pool' => $this->stash()->get('planning.finance.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->end();
    }
}
