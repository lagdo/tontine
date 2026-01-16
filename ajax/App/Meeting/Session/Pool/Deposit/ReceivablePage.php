<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\Session\PageComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

#[Before('getPool')]
class ReceivablePage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'receivable.page'];

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param DepositService $depositService
     */
    public function __construct(protected DepositService $depositService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $search = $this->bag('meeting')->get('receivable.search', '');
        $filter = $this->bag('meeting')->get('receivable.filter');

        return $this->depositService->getReceivableCount($pool, $session, $filter, $search);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $search = $this->bag('meeting')->get('receivable.search', '');
        $filter = $this->bag('meeting')->get('receivable.filter');

        return $this->renderTpl('pages.meeting.session.deposit.receivable.page', [
            'pool' => $pool,
            'session' => $session,
            'receivables' => $this->depositService
                ->getReceivables($pool, $session, $filter, $search, $this->currentPage()),
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
