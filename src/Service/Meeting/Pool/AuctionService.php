<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Siak\Tontine\Service\TenantService;

class AuctionService
{
    /**
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     * @param SessionService $sessionService
     */
    public function __construct(private TenantService $tenantService,
        private LocaleService $localeService, private SessionService $sessionService)
    {}

    /**
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return Builder|Relation
     */
    private function getQuery(Session $session, ?bool $onlyPaid = null): Builder|Relation
    {
        $prevSessions = $this->sessionService
            ->getSessionIds($session->round, $session, withCurr: false);

        return Auction::when($onlyPaid !== null, fn(Builder $qp) =>
                $qp->where('paid', $onlyPaid))
            ->where(function(Builder $query) use($session, $prevSessions) {
                // Take all the auctions in the current session
                $query->whereHas('remitment', fn(Builder $qr) =>
                    $qr->whereHas('payable', fn(Builder $qp) =>
                        $qp->where('session_id', $session->id)));
                if($prevSessions->count() === 0)
                {
                    return;
                }

                // The auctions in the previous sessions.
                $query->orWhere(fn(Builder $qa) => $qa
                    ->whereHas('remitment', fn(Builder $qr) =>
                        $qr->whereHas('payable', fn(Builder $qp) =>
                            $qp->whereIn('session_id', $prevSessions)))
                    ->where(function(Builder $query) use($session) {
                        // The auctions that are not yet paid.
                        $query->orWhere('paid', false);
                        // The auctions that are paid in the current session.
                        $query->orWhere(fn(Builder $qs) => $qs
                            ->where('paid', true)
                            ->where('session_id', $session->id));
                    }));
            });
    }

    /**
     * Get the number of auctions.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return int
     */
    public function getAuctionCount(Session $session, ?bool $onlyPaid): int
    {
        return $this->getQuery($session, $onlyPaid)->count();
    }

    /**
     * Get the auctions.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getAuctions(Session $session, ?bool $onlyPaid, int $page = 0): Collection
    {
        return $this->getQuery($session, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['remitment.payable.session', 'remitment.payable.subscription.member'])
            ->get()
            ->each(fn(Auction $auction) =>
                $auction->member = $auction->remitment->payable->subscription->member)
            ->sortBy('member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * Set or unset the auction payment.
     *
     * @param Session $session The session
     * @param int $auctionId
     *
     * @return void
     */
    public function toggleAuctionPayment(Session $session, int $auctionId)
    {
        $auction = $this->getQuery($session)->find($auctionId);
        if(($auction))
        {
            $auction->update(['paid' => !$auction->paid]);
        }
    }
}
