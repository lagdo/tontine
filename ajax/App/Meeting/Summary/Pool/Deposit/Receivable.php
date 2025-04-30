<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Stringable;

/**
 * @before getPool
 */
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
    public function pool(int $poolId)
    {
        $this->bag('summary')->set('deposit.page', 1);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.deposit.receivable.home', [
            'pool' => $this->stash()->get('summary.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(ReceivablePage::class)->page();
        $this->showTotal();
    }
}
