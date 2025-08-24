<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Early;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\Deposit\Deposit;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Stringable;

/**
 * @before getPool [false]
 * @before getNextSession
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
     * @param int $sessionId
     *
     * @return mixed
     */
    public function pool(int $poolId, int $sessionId): void
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
            'session' => $this->stash()->get('summary.early.session'),
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

    public function toggleFilter(): void
    {
        $onlyUnpaid = $this->bag('summary')->get('summary.early.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('summary')->set('summary.early.filter', $onlyUnpaid);
        $this->bag('summary')->set('summary.early.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }
}
