<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Charge\PageComponent;

class SettlementPage extends PageComponent
{
    use ChargeTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'settlement.fixed.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('meeting')->get('settlement.fixed.search', '');
        $filter = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->billService->getBillCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = $this->bag('meeting')->get('settlement.fixed.search', '');
        $filter = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->renderTpl('pages.meeting.session.charge.fixed.settlement.page', [
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
        $this->response->jo('tontine')->makeTableResponsive('content-session-fee-fixed-bills');
    }
}
