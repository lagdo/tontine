<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Late;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Meeting\Summary\Pool\Deposit\Deposit;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;

#[Before('getPool', [false])]
class Receivable extends Component
{
    use PoolTrait;
    use DepositTrait;

    /**
     * @var string
     */
    protected string $overrides = Deposit::class;

    /**
     * @param int $poolId
     *
     * @return mixed
     */
    public function pool(int $poolId): void
    {
        $this->bag('summary')->set('receivable.late.page', 1);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.summary.deposit.late.receivable.home', [
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

    public function toggleFilter(): void
    {
        $onlyUnpaid = $this->bag('summary')->get('receivable.late.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('summary')->set('receivable.late.filter', $onlyUnpaid);
        $this->bag('summary')->set('receivable.late.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }
}
