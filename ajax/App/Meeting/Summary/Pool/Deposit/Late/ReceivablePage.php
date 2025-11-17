<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Late;

use Ajax\App\Meeting\Summary\PageComponent;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\Meeting\Pool\LateDepositService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

#[Before('getPool', [false])]
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
     * @param SessionService $sessionService
     * @param LateDepositService $depositService
     */
    public function __construct(protected SessionService $sessionService,
        protected LateDepositService $depositService)
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
        $this->response->jo('tontine')->makeTableResponsive('content-meeting-receivables');
    }
}
