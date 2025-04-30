<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\Session\FuncComponent;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

/**
 * @databag auction
 */
class AuctionFunc extends FuncComponent
{
    /**
     * The constructor
     *
     * @param AuctionService $auctionService
     */
    public function __construct(private DebtValidator $validator,
        private AuctionService $auctionService)
    {}

    public function togglePayment(string $auctionId)
    {
        $this->validator->validate($auctionId);

        $session = $this->stash()->get('meeting.session');
        $this->auctionService->toggleAuctionPayment($session, $auctionId);

        $this->cl(AuctionPage::class)->page();
    }
}
