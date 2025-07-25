<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\Session\PageComponent;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Stringable;

/**
 * @databag meeting.auction
 */
class AuctionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.auction', 'page'];

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
        $session = $this->stash()->get('meeting.session');
        $filtered = $this->bag('meeting.auction')->get('filter', null);

        return $this->auctionService->getAuctionCount($session, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $filtered = $this->bag('meeting.auction')->get('filter', null);

        return $this->renderView('pages.meeting.session.auction.page', [
            'session' => $session,
            'auctions' => $this->auctionService
                ->getAuctions($session, $filtered, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-auctions-page');
    }
}
