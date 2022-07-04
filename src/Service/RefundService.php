<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;

use Siak\Tontine\Model\Bidding;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;

class RefundService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
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
     * @param Session $session The session
     * @param bool $refunded
     *
     * @return mixed
     */
    private function getBiddingQuery(Session $session, ?bool $refunded)
    {
        // Get the biddings of the previous sessions.
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        $biddings = Bidding::whereIn('session_id', $sessionIds);
        if($refunded === false)
        {
            $biddings->whereDoesntHave('refund');
        }
        elseif($refunded === true)
        {
            $biddings->whereHas('refund');
        }
        return $biddings;
    }

    /**
     * Get the number of bids that are not yet refunded.
     *
     * @param Session $session The session
     * @param bool $refunded
     *
     * @return int
     */
    public function getBiddingCount(Session $session, ?bool $refunded): int
    {
        return $this->getBiddingQuery($session, $refunded)->count();
    }

    /**
     * Get the bids that are not yet refunded.
     *
     * @param Session $session The session
     * @param bool $refunded
     * @param int $page
     *
     * @return Collection
     */
    public function getBiddings(Session $session, ?bool $refunded, int $page = 0): Collection
    {
        $biddings = $this->getBiddingQuery($session, $refunded);
        if($page > 0 )
        {
            $biddings->take($this->tenantService->getLimit());
            $biddings->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $biddings->with(['refund', 'member'])->get();
    }

    /**
     * Get the refunds for a given session.
     *
     * @param Session $session The session
     * @param int $page
     *
     * @return Collection
     */
    public function getRefunds(Session $session, int $page = 0): Collection
    {
        $refunds = $session->refunds();
        if($page > 0 )
        {
            $refunds->take($this->tenantService->getLimit());
            $refunds->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $refunds->with('bidding.member')->get();
    }

    /**
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $biddingId
     *
     * @return void
     */
    public function createRefund(Session $session, int $biddingId): void
    {
        $sessionIds = $this->tenantService->round()->sessions()->pluck('id');
        $bidding = Bidding::whereIn('session_id', $sessionIds)->find($biddingId);
        $refund = new Refund();
        $refund->bidding()->associate($bidding);
        $refund->session()->associate($session);
        $refund->save();
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $refundId
     *
     * @return void
     */
    public function deleteRefund(Session $session, int $refundId): void
    {
        $session->refunds()->where('id', $refundId)->delete();
    }

    /**
     * Get the refund sum.
     *
     * @param Session $session The session
     *
     * @return string
     */
    public function getRefundSum(Session $session): string
    {
        return Currency::format($session->refunds()
            ->join('biddings', 'biddings.id', '=', 'refunds.bidding_id')
            ->sum('biddings.amount_bid'));
    }
}
