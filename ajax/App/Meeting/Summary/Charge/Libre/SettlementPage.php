<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

use Ajax\App\Meeting\Summary\Charge\PageComponent;
use Stringable;

class SettlementPage extends PageComponent
{
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
        $search = '';
        $filter = $this->bag('summary')->get('settlement.libre.filter', null);
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');

        return $this->billService->getBillCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = '';
        $filter = $this->bag('summary')->get('settlement.libre.filter', null);
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');

        return $this->renderView('pages.meeting.summary.charge.libre.settlement.page', [
            'session' => $session,
            'charge' => $charge,
            'bills' => $this->billService->getBills($charge, $session, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-fee-libre-bills');
    }
}
