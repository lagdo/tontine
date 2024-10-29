<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

use App\Ajax\Web\Meeting\Session\Charge\ChargePageComponent;

class SettlementPage extends ChargePageComponent
{
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
        $search = '';
        $filter = $this->bag('meeting')->get('settlement.libre.filter', null);
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return $this->billService->getBillCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = '';
        $filter = $this->bag('meeting')->get('settlement.libre.filter', null);
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return (string)$this->renderView('pages.meeting.settlement.page', [
            'session' => $session,
            'charge' => $charge,
            'bills' => $this->billService->getBills($charge, $session, $search, $filter, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-fee-libre-bills');
    }
}
