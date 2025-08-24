<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Early;

use Ajax\App\Meeting\Session\PageComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\EarlyDepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

/**
 * @before getPool [false]
 * @before getNextSession
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
    protected array $bagOptions = ['meeting', 'session.early.page'];

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
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $nextSession = $this->stash()->get('meeting.early.session');
        $filter = $this->bag('meeting')->get('session.early.filter');

        return $this->depositService->getReceivableCount($pool,
            $session, $nextSession, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $nextSession = $this->stash()->get('meeting.early.session');
        $filter = $this->bag('meeting')->get('session.early.filter');

        return $this->renderView('pages.meeting.session.deposit.early.receivable.page', [
            'pool' => $pool,
            'session' => $session,
            'sessionStatuses' => $this->sessionService->getSessionStatuses(),
            'receivables' => $this->depositService->getReceivables($pool,
                $session, $nextSession, $filter, $this->currentPage()),
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
