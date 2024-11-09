<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Fixed;

use App\Ajax\Web\Meeting\Session\Charge\ChargePageComponent;

use function trim;

class SettlementPage extends ChargePageComponent
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
        $search = trim($this->bag('meeting')->get('settlement.fixed.search', ''));
        $filter = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return $this->billService->getBillCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = trim($this->bag('meeting')->get('settlement.fixed.search', ''));
        $filter = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return (string)$this->renderView('pages.meeting.charge.fixed.settlement.page', [
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
        $this->response->js()->makeTableResponsive('meeting-fee-fixed-bills');
    }
}
