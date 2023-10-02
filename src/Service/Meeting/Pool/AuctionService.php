<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class AuctionService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     */
    public function __construct(TenantService $tenantService, LocaleService $localeService)
    {
        $this->tenantService = $tenantService;
        $this->localeService = $localeService;
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * @param int $sessionId
     * @param Collection $prevSessions
     * @param bool $onlyPaid
     *
     * @return Builder
     */
    private function getQuery(int $sessionId, Collection $prevSessions, ?bool $onlyPaid = null): Builder
    {
        return Auction::when($onlyPaid !== null, function(Builder $query) use($onlyPaid) {
                return $query->where('paid', $onlyPaid);
            })
            ->where(function(Builder $query) use($sessionId, $prevSessions) {
                // Take all the auctions in the current session
                $query->whereHas('remitment', function(Builder $query) use($sessionId) {
                    $query->whereHas('payable', function(Builder $query) use($sessionId) {
                        $query->where('session_id', $sessionId);
                    });
                });
                if($prevSessions->count() === 0)
                {
                    return;
                }
                // The auctions in the previous sessions.
                $query->orWhere(function(Builder $query) use($sessionId, $prevSessions) {
                    $query->whereHas('remitment', function(Builder $query) use($prevSessions) {
                        $query->whereHas('payable', function(Builder $query) use($prevSessions) {
                            $query->whereIn('session_id', $prevSessions);
                        });
                    })
                    ->where(function(Builder $query) use($sessionId) {
                        // The auctions that are not yet paid.
                        $query->orWhere('paid', false);
                        // The auctions that are paid in the current session.
                        $query->orWhere(function(Builder $query) use($sessionId) {
                            $query->where('paid', true)->where('session_id', $sessionId);
                        });
                    });
                });
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
    public function getAuctionCount(Session $session, ?bool $onlyPaid = null): int
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');

        return $this->getQuery($session->id, $prevSessions, $onlyPaid)->count();
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
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');

        return $this->getQuery($session->id, $prevSessions, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['remitment.payable.session', 'remitment.payable.subscription.member'])
            ->get()
            ->each(function(Auction $auction) {
                $auction->member = $auction->remitment->payable->subscription->member;
            })
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
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        $auction = $this->getQuery($session->id, $prevSessions)->find($auctionId);
        if(($auction))
        {
            $auction->update(['paid' => !$auction->paid]);
        }
    }
}
