<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

#[Before('getPool')]
#[Databag('planning.pool')]
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
            'pool' => $this->stash()->get('planning.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->end();
    }
}
