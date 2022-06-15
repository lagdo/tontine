<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Siak\Tontine\Model\Bidding;
use Siak\Tontine\Model\Remittance;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Fund;

class BiddingService
{
    use Figures\TableTrait;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var RemittanceService
     */
    protected RemittanceService $remittanceService;

    /**
     * @var SubscriptionService
     */
    protected SubscriptionService $subscriptionService;

    /**
     * @param TenantService $tenantService
     * @param RemittanceService $remittanceService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(TenantService $tenantService,
        RemittanceService $remittanceService, SubscriptionService $subscriptionService)
    {
        $this->tenantService = $tenantService;
        $this->remittanceService = $remittanceService;
        $this->subscriptionService = $subscriptionService;
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
     * Get the unpaid subscriptions of a given fund.
     *
     * @param Fund $fund
     *
     * @return Collection
     */
    public function getPendingSubscriptions(Fund $fund): Collection
    {
        return $fund->subscriptions()->with(['payable', 'member'])->get()
            ->filter(function($subscription) {
                return !$subscription->payable->session_id;
            });
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
        return Remittance::whereIn('payable_id',
            $session->payables()->pluck('id'))->sum('amount_paid') +
            $session->biddings->reduce(function($sum, $bidding) {
                return $sum + $bidding->amount_paid - $bidding->amount_bid;
            }, 0);
    }

    /**
     * Create a remittance.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $subscriptionId
     * @param int $amountPaid
     *
     * @return void
     */
    public function createRemittance(Fund $fund, Session $session, int $subscriptionId, int $amountPaid): void
    {
        $subscription = $fund->subscriptions()->find($subscriptionId);
        DB::transaction(function() use($fund, $session, $subscription, $amountPaid) {
            $this->subscriptionService->setPayableSession($session, $subscription);
            $this->remittanceService->createRemittance($fund, $session, $subscription->payable->id, $amountPaid);
        });
    }

    /**
     * Delete a remittance.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $subscriptionId
     *
     * @return void
     */
    public function deleteRemittance(Fund $fund, Session $session, int $subscriptionId): void
    {
        $subscription = $fund->subscriptions()->find($subscriptionId);
        DB::transaction(function() use($fund, $session, $subscription) {
            $this->remittanceService->deleteRemittance($fund, $session, $subscription->payable->id);
            $this->subscriptionService->unsetPayableSession($session, $subscription);
        });
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
     * Create a bidding.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $payableId
     * @param int $amount
     *
     * @return void
     */
    public function createBidding(Fund $fund, Session $session, int $payableId, int $amount): void
    {
        $payable = $this->remittanceService->getPayable($fund, $session, $payableId);
        if(!$payable || $payable->remittance)
        {
            return;
        }
        $payable->remittance()->create(['paid_at' => now(), 'amount_paid' => $amount]);
        $session->biddings()->create([]);
    }

    /**
     * Delete a bidding.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $payableId
     *
     * @return void
     */
    public function deleteBidding(Fund $fund, Session $session, int $payableId): void
    {
        $payable = $this->remittanceService->getPayable($fund, $session, $payableId);
        if(!$payable || !$payable->remittance)
        {
            return;
        }
        $payable->remittance()->delete();
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
