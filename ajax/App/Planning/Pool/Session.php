<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;

#[Before('getPool')]
#[Databag('planning.pool')]
#[Export(base: ['render'])]
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

    public function html(): string
    {
        return $this->renderTpl('pages.planning.pool.session.home', [
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
