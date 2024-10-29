<?php

namespace App\Ajax\Web\Meeting\Session\Pool\Deposit;

use App\Ajax\Web\Meeting\MeetingPageComponent;
use App\Ajax\Web\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

/**
 * @before getPool
 */
class PoolPage extends MeetingPageComponent
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
        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');

        return $this->depositService->getReceivableCount($pool, $session);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');

        return (string)$this->renderView('pages.meeting.deposit.pool.page', [
            'pool' => $pool,
            'session' => $session,
            'receivables' => $this->depositService->getReceivables($pool, $session, $this->page),
        ]);
    }

    private function showTotal()
    {
        $session = $this->cache->get('meeting.session');
        $pool = $this->cache->get('meeting.pool');
        $this->cache->set('meeting.pool.deposit.count',
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
