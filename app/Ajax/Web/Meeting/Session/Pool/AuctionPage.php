<?php

namespace App\Ajax\Web\Meeting\Session\Pool;

use App\Ajax\Web\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;

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
    public function html(): string
    {
        $session = $this->cache->get('meeting.session');
        $filtered = $this->bag('auction')->get('filter', null);

        return (string)$this->renderView('pages.meeting.auction.page', [
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
