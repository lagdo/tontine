<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

#[Databag('meeting.auction')]
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
        return $this->renderView('pages.meeting.session.auction.home', [
            'session' => $this->stash()->get('meeting.session'),
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
        $filtered = $this->bag('meeting.auction')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('meeting.auction')->set('filter', $filtered);
        $this->bag('meeting.auction')->set('page', 1);

        $this->cl(AuctionPage::class)->page(1);
    }
}
