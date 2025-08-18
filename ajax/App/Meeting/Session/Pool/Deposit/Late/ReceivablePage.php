<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Late;

use Ajax\App\Meeting\Session\PageComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

/**
 * @before getPool [false]
 */
class ReceivablePage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'receivable.late.page'];

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param DepositService $depositService
     * @param SessionService $sessionService
     */
    public function __construct(protected PoolService $poolService,
        protected DepositService $depositService, protected SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $filter = $this->bag('meeting')->get('receivable.late.filter');

        return $this->depositService->getLateReceivableCount($pool, $session, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $filter = $this->bag('meeting')->get('receivable.late.filter');

        return $this->renderView('pages.meeting.session.deposit.late.receivable.page', [
            'pool' => $pool,
            'session' => $session,
            'sessionStatuses' => $this->sessionService->getSessionStatuses(),
            'receivables' => $this->depositService
                ->getLateReceivables($pool, $session, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-meeting-receivables');
    }
}
