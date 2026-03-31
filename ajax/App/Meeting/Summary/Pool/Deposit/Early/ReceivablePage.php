<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Early;

use Ajax\App\Meeting\Summary\PageComponent;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\Meeting\Pool\EarlyDepositService;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Before('getPool', [false])]
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
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');

        return $this->depositService->getReceivableCount($pool,
            $session, null, true);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');

        return $this->renderTpl('pages.meeting.summary.deposit.early.receivable.page', [
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
        $this->response()->jo('tontine')->makeTableResponsive('content-meeting-receivables');
    }
}
