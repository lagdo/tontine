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
    public function html(): string
    {
        $search = trim($this->bag('meeting')->get('settlement.fixed.search', ''));
        $filter = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return (string)$this->renderView('pages.meeting.settlement.page', [
            'session' => $session,
            'charge' => $charge,
            'bills' => $this->billService->getBills($charge, $session, $search, $filter, $this->page),
        ]);
    }

    protected function count(): int
    {
        $search = trim($this->bag('meeting')->get('settlement.fixed.search', ''));
        $filter = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return $this->billService->getBillCount($charge, $session, $search, $filter);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-fee-fixed-bills');

        return $this->response;
    }
}
