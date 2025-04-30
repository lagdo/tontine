<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

use Ajax\App\Meeting\Summary\Component;
use Stringable;

/**
 * @databag auction
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

        $this->cl(AuctionPage::class)->page(1);
    }
}
