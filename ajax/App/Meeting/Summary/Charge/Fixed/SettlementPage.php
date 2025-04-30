<?php

namespace Ajax\App\Meeting\Summary\Charge\Fixed;

use Ajax\App\Meeting\Summary\Charge\PageComponent;
use Stringable;

class SettlementPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary', 'settlement.fixed.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('summary')->get('settlement.fixed.search', '');
        $filter = $this->bag('summary')->get('settlement.fixed.filter', null);
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');

        return $this->billService->getBillCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = $this->bag('summary')->get('settlement.fixed.search', '');
        $filter = $this->bag('summary')->get('settlement.fixed.filter', null);
        $session = $this->stash()->get('summary.session');
        $charge = $this->stash()->get('summary.session.charge');

        return $this->renderView('pages.meeting.summary.charge.fixed.settlement.page', [
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
        $this->response->js('Tontine')->makeTableResponsive('content-session-fee-fixed-bills');
    }
}
