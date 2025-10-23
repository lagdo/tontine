<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Late;

use Ajax\App\Meeting\Session\PageComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
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
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $filter = $this->bag('meeting')->get('receivable.late.filter');

        return $this->depositService->getReceivableCount($pool, $session, $filter);
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
