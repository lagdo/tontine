<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

use Ajax\App\Meeting\Summary\Component;
use Stringable;

/**
 * @databag summary.auction
 */
class Auction extends Component
{
    /**
     * @var string
     */
    protected $overrides = Remitment::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.auction.home', [
            'session' => $this->stash()->get('summary.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(AuctionPage::class)->page();
    }

    public function toggleFilter(): void
    {
        $filtered = $this->bag('summary.auction')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('summary.auction')->set('filter', $filtered);
        $this->bag('summary.auction')->set('page', 1);

        $this->cl(AuctionPage::class)->page(1);
    }
}
