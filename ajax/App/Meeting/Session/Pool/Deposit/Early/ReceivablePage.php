<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Early;

use Ajax\App\Meeting\Session\PageComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\Meeting\Pool\EarlyDepositService;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Before('getPool', [false])]
#[Before('getNextSession')]
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
     * @param SessionService $sessionService
     * @param EarlyDepositService $depositService
     */
    public function __construct(protected SessionService $sessionService,
        protected EarlyDepositService $depositService)
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
    public function html(): string
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $nextSession = $this->stash()->get('meeting.early.session');
        $filter = $this->bag('meeting')->get('session.early.filter');

        return $this->renderTpl('pages.meeting.session.deposit.early.receivable.page', [
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
        $this->response->jo('tontine')->makeTableResponsive('content-meeting-receivables');
    }
}
