<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Charge\PageComponent;
use Stringable;

class SettlementPage extends PageComponent
{
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
    public function html(): Stringable
    {
        $search = $this->bag('meeting')->get('settlement.fixed.search', '');
        $filter = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->renderView('pages.meeting.session.charge.fixed.settlement.page', [
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
        $this->response->jo('Tontine')->makeTableResponsive('content-session-fee-fixed-bills');
    }
}
