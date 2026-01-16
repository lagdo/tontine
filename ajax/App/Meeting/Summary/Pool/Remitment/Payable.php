<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;

#[Before('getPool')]
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
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.summary.remitment.payable.home', [
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
