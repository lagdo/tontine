<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Stringable;

#[Before('checkChargeEdit')]
#[Before('setSettlement')]
class SettlementPage extends PageComponent
{
    use SettlementTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'settlement.libre.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->stash()->get('meeting.session.bill.count');
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = '';
        $filter = $this->bag('meeting')->get('settlement.libre.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->renderView('pages.meeting.session.charge.libre.settlement.page', [
            'session' => $session,
            'charge' => $charge,
            'bills' => $this->billService->getBills($charge, $session,
                $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-fee-libre-bills');
    }
}
