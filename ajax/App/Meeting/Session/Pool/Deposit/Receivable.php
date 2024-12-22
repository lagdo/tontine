<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\MeetingComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Stringable;

/**
 * @before getPool
 */
class Receivable extends MeetingComponent
{
    use PoolTrait;
    use DepositTrait;

    /**
     * @var string
     */
    protected $overrides = Deposit::class;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param DepositService $depositService
     */
    public function __construct(protected PoolService $poolService,
        protected DepositService $depositService)
    {}

    /**
     * @param int $poolId
     *
     * @return mixed
     */
    public function pool(int $poolId)
    {
        $this->bag('meeting')->set('deposit.page', 1);

        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.deposit.receivable.home', [
            'pool' => $this->cache()->get('meeting.pool'),
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

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function addDeposit(int $receivableId)
    {
        $pool = $this->cache()->get('meeting.pool');
        $session = $this->cache()->get('meeting.session');
        $this->depositService->createDeposit($pool, $session, $receivableId);

        return $this->cl(ReceivablePage::class)->page();
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function delDeposit(int $receivableId)
    {
        $pool = $this->cache()->get('meeting.pool');
        $session = $this->cache()->get('meeting.session');
        $this->depositService->deleteDeposit($pool, $session, $receivableId);

        return $this->cl(ReceivablePage::class)->page();
    }

    /**
     * @return mixed
     */
    public function addAllDeposits()
    {
        $pool = $this->cache()->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return $this->response;
        }

        $session = $this->cache()->get('meeting.session');
        $this->depositService->createAllDeposits($pool, $session);

        return $this->cl(ReceivablePage::class)->page();
    }

    /**
     * @return mixed
     */
    public function delAllDeposits()
    {
        $pool = $this->cache()->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return $this->response;
        }

        $session = $this->cache()->get('meeting.session');
        $this->depositService->deleteAllDeposits($pool, $session);

        return $this->cl(ReceivablePage::class)->page();
    }
}
