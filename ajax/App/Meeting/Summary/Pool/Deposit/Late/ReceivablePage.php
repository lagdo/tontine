<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Late;

use Ajax\App\Meeting\Summary\PageComponent;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\LateDepositService;
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
     * @param LateDepositService $depositService
     * @param SessionService $sessionService
     */
    public function __construct(protected PoolService $poolService,
        protected LateDepositService $depositService, protected SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');
        $filter = $this->bag('summary')->get('receivable.late.filter');

        return $this->depositService->getReceivableCount($pool, $session, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');
        $filter = $this->bag('summary')->get('receivable.late.filter');

        return $this->renderView('pages.meeting.summary.deposit.late.receivable.page', [
            'pool' => $pool,
            'session' => $session,
            'sessionStatuses' => $this->sessionService->getSessionStatuses(),
            'receivables' => $this->depositService->getReceivables($pool,
                $session, $filter, $this->currentPage()),
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
