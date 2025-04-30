<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\Session\Component;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
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
        $this->bag('meeting')->set('deposit.page', 1);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.deposit.receivable.home', [
            'pool' => $this->stash()->get('meeting.pool'),
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
        $onlyUnpaid = $this->bag('meeting')->get('deposit.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('deposit.filter', $onlyUnpaid);
        $this->bag('meeting')->set('deposit.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('deposit.search', trim($search));
        $this->bag('meeting')->set('deposit.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }
}
