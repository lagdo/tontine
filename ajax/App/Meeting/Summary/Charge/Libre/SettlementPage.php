<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

use Ajax\App\Meeting\Summary\Charge\PageComponent;

class SettlementPage extends PageComponent
{
    use ChargeTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary', 'settlement.libre.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('summary')->get('settlement.libre.search', '');
        $filter = $this->bag('summary')->get('settlement.libre.filter', null);
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');

        return $this->billService->getBillCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = $this->bag('summary')->get('settlement.libre.search', '');
        $filter = $this->bag('summary')->get('settlement.libre.filter', null);
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');

        return $this->renderTpl('pages.meeting.summary.charge.libre.settlement.page', [
            'session' => $session,
            'charge' => $charge,
            'bills' => $this->billService->getBills($charge, $session, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-session-fee-libre-bills');
    }
}
