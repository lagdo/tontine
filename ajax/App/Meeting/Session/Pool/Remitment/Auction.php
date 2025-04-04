<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\Component;
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
        return $this->renderView('pages.meeting.auction.home', [
            'session' => $this->stash()->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(AuctionPage::class)->page();
    }
}
