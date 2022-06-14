<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Bidding;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Fund;

use Illuminate\Support\Collection;

use function trans;

class BiddingService
{
    use Figures\TableTrait;

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
     * Get a single fund.
     *
     * @param int $fundId    The fund id
     *
     * @return Fund|null
     */
    public function getFund(int $fundId): ?Fund
    {
        return $this->tenantService->round()->funds()->find($fundId);
    }

    /**
     * Get the bids for a given fund in a given session.
     *
     * @param Fund $fund    The fund
     * @param Session $session    The session
     *
     * @return Collection
     */
    public function getOpenedBids(Fund $fund, Session $session): Collection
    {
        $query = $session->payables()->whereDoesntHave('remittance');
        if($fund !== null)
        {
            $query->whereIn('subscription_id', $fund->subscriptions()->pluck('id'));
        }
        return $query->get()->map(function($payable) {
            return (object)[
                'id' => $payable->id,
                'title' => $payable->subscription->fund->title,
                'amount' => $payable->subscription->fund->amount,
            ];
        });
    }

    /**
     * Get the amount available for bidding.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getAmountAvailable(Session $session): int
    {
        return $session->biddings->reduce(function($sum, $bidding) {
            return $sum + $bidding->amount_paid - $bidding->amount_bid;
        }, 0);
    }

    /**
     * Get the bids for a given session.
     *
     * @param Session $session    The session
     * @param int $page
     *
     * @return Collection
     */
    public function getBiddings(Session $session, int $page = 0): Collection
    {
        $biddings = $session->biddings();
        if($page > 0 )
        {
            $biddings->take($this->tenantService->getLimit());
            $biddings->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $biddings->get();
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
        $bidding = Bidding::whereIn('session_id',
            $this->tenantService->round()->sessions()->pluck('id'))->find($biddingId);
        if(!$bidding || $bidding->refund)
        {
            return;
        }
        $bidding->refund()->create(['session' => $session]);
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $biddingId
     *
     * @return void
     */
    public function deleteRefund(Session $session, int $biddingId): void
    {
        $bidding = $session->biddings()->find($biddingId);
        if(!$bidding || !$bidding->refund)
        {
            return;
        }
        $bidding->refund()->delete();
    }
}
