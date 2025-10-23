<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Late;

use Ajax\App\Meeting\Session\Component;
use Ajax\App\Meeting\Session\Pool\Deposit\Deposit;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
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
        $this->bag('meeting')->set('receivable.late.page', 1);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.deposit.late.receivable.home', [
            'pool' => $this->stash()->get('meeting.pool'),
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
        $onlyUnpaid = $this->bag('meeting')->get('receivable.late.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('receivable.late.filter', $onlyUnpaid);
        $this->bag('meeting')->set('receivable.late.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }
}
