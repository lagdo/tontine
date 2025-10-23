<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Early;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\Deposit\Deposit;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Stringable;

#[Before('getPool', [false])]
class Receivable extends Component
{
    use PoolTrait;
    use DepositTrait;

    /**
     * @var string
     */
    protected $overrides = Deposit::class;

    /**
     * @param int $poolId
     *
     * @return mixed
     */
    public function pool(int $poolId): void
    {
        $this->bag('summary')->set('summary.early.page', 1);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.deposit.early.receivable.home', [
            'pool' => $this->stash()->get('summary.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(ReceivablePage::class)->page();
        $this->showTotal();
    }
}
