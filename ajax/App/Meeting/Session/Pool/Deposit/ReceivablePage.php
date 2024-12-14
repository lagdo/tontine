<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\MeetingPageComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Stringable;

/**
 * @before getPool
 */
class ReceivablePage extends MeetingPageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'deposit.page'];

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
     * @inheritDoc
     */
    protected function count(): int
    {
        $pool = $this->cache()->get('meeting.pool');
        $session = $this->cache()->get('meeting.session');

        return $this->depositService->getReceivableCount($pool, $session);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->cache()->get('meeting.pool');
        $session = $this->cache()->get('meeting.session');

        return $this->renderView('pages.meeting.deposit.receivable.page', [
            'pool' => $pool,
            'session' => $session,
            'receivables' => $this->depositService->getReceivables($pool, $session, $this->pageNumber()),
        ]);
    }

    private function showTotal()
    {
        $session = $this->cache()->get('meeting.session');
        $pool = $this->cache()->get('meeting.pool');
        $this->cache()->set('meeting.pool.deposit.count',
            $this->depositService->countDeposits($pool, $session));

        $this->cl(Total::class)->render();
        $this->cl(Action::class)->render();
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-pool-deposits');
        $this->showTotal();
    }
}
