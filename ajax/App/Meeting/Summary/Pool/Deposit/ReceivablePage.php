<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit;

use Ajax\App\Meeting\Summary\PageComponent;
use Ajax\App\Meeting\Summary\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Stringable;

#[Before('getPool')]
class ReceivablePage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary', 'receivable.page'];

    /**
     * The constructor
     *
     * @param DepositService $depositService
     */
    public function __construct(protected DepositService $depositService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');
        $search = $this->bag('summary')->get('receivable.search', '');
        $filter = $this->bag('summary')->get('receivable.filter');

        return $this->depositService->getReceivableCount($pool, $session, $filter, $search);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');
        $search = $this->bag('summary')->get('receivable.search', '');
        $filter = $this->bag('summary')->get('receivable.filter');

        return $this->renderView('pages.meeting.summary.deposit.receivable.page', [
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
