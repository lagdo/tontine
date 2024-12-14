<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Stringable;

/**
 * @databag auction
 */
class Auction extends MeetingComponent
{
    /**
     * @var string
     */
    protected $overrides = Remitment::class;

    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

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
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.auction.home', [
            'session' => $this->cache()->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(AuctionPage::class)->page();
    }

    public function toggleFilter()
    {
        $filtered = $this->bag('auction')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('auction')->set('filter', $filtered);

        return $this->cl(AuctionPage::class)->page(1);
    }

    /**
     * @di $validator
     */
    public function togglePayment(string $auctionId)
    {
        $this->validator->validate($auctionId);

        $session = $this->cache()->get('meeting.session');
        $this->auctionService->toggleAuctionPayment($session, $auctionId);

        return $this->cl(AuctionPage::class)->page();
    }
}
