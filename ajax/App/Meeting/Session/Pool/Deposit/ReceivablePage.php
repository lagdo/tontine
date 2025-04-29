<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\Session\PageComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Stringable;

/**
 * @before getPool
 */
class ReceivablePage extends PageComponent
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
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');

        return $this->depositService->getReceivableCount($pool, $session, null);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');

        return $this->renderView('pages.meeting.session.deposit.receivable.page', [
            'pool' => $pool,
            'session' => $session,
            'receivables' => $this->depositService
                ->getReceivables($pool, $session, null, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-meeting-receivables');
    }
}
