<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\FuncComponent;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

/**
 * @databag auction
 */
class AuctionFunc extends FuncComponent
{
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

    public function toggleFilter()
    {
        $filtered = $this->bag('auction')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('auction')->set('filter', $filtered);

        $this->cl(AuctionPage::class)->page(1);
    }

    /**
     * @di $validator
     */
    public function togglePayment(string $auctionId)
    {
        $this->validator->validate($auctionId);

        $session = $this->stash()->get('meeting.session');
        $this->auctionService->toggleAuctionPayment($session, $auctionId);

        $this->cl(AuctionPage::class)->page();
    }
}
