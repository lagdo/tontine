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

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('summary')->get('deposit.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('summary')->set('deposit.filter', $onlyUnpaid);
        $this->bag('summary')->set('deposit.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('summary')->set('deposit.search', trim($search));
        $this->bag('summary')->set('deposit.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }
}
