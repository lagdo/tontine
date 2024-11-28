<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Stringable;

/**
 * @databag auction
 */
class AuctionPage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['auction', 'page'];

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
        $session = $this->cache->get('meeting.session');
        $filtered = $this->bag('auction')->get('filter', null);

        return $this->auctionService->getAuctionCount($session, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache->get('meeting.session');
        $filtered = $this->bag('auction')->get('filter', null);

        return $this->renderView('pages.meeting.auction.page', [
            'session' => $session,
            'auctions' => $this->auctionService->getAuctions($session, $filtered, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-auctions-page');
    }
}
