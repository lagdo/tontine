<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Stringable;

/**
 * @before getPool
 */
class Payable extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = Remitment::class;

    public function pool(int $poolId): void
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.remitment.payable.home', [
            'pool' => $this->stash()->get('summary.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(Total::class)->render();
        $this->cl(PayablePage::class)->render();
    }
}
