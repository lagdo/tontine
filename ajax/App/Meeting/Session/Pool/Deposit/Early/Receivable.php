<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Early;

use Ajax\App\Meeting\Session\Component;
use Ajax\App\Meeting\Session\Pool\Deposit\Deposit;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Stringable;

#[Before('getPool', [false])]
#[Before('getNextSession')]
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
        $this->bag('meeting')->set('session.early.page', 1);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.deposit.early.receivable.home', [
            'pool' => $this->stash()->get('meeting.pool'),
            'session' => $this->stash()->get('meeting.early.session'),
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
        $onlyUnpaid = $this->bag('meeting')->get('session.early.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('session.early.filter', $onlyUnpaid);
        $this->bag('meeting')->set('session.early.page', 1);

        $this->cl(ReceivablePage::class)->page();
    }
}
