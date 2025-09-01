<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Early;

use Ajax\App\Meeting\Summary\PageComponent;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\EarlyDepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

/**
 * @before getPool [false]
 */
class ReceivablePage extends PageComponent
{
    use PoolTrait;
    use DepositTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'summary.early.page'];

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param EarlyDepositService $depositService
     * @param SessionService $sessionService
     */
    public function __construct(protected PoolService $poolService,
        protected EarlyDepositService $depositService,
        protected SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');

        return $this->depositService->getReceivableCount($pool,
            $session, null, true);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.deposit.early.receivable.page', [
            'pool' => $pool,
            'session' => $session,
            'sessionStatuses' => $this->sessionService->getSessionStatuses(),
            'receivables' => $this->depositService->getReceivables($pool,
                $session, null, true, $this->currentPage()),
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
