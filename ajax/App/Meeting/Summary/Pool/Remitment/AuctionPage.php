<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

use Ajax\App\Meeting\Summary\PageComponent;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Stringable;

/**
 * @databag summary.auction
 */
class AuctionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary.auction', 'page'];

    /**
     * The constructor
     *
     * @param AuctionService $auctionService
     */
    public function __construct(private AuctionService $auctionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('summary.session');
        $filtered = $this->bag('summary.auction')->get('filter', null);
        return $this->auctionService->getAuctionCount($session, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        $filtered = $this->bag('summary.auction')->get('filter', null);
        return $this->renderView('pages.meeting.summary.auction.page', [
            'session' => $session,
            'auctions' => $this->auctionService
                ->getAuctions($session, $filtered, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-auctions-page');
    }
}
