<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Siak\Tontine\Model\Bidding;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Remittance;
use Siak\Tontine\Model\Session;

use function collect;

class BiddingService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PlanningService
     */
    protected PlanningService $planningService;

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
     * @param PlanningService $planningService
     * @param RemittanceService $remittanceService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(TenantService $tenantService, PlanningService $planningService,
        RemittanceService $remittanceService, SubscriptionService $subscriptionService)
    {
        $this->tenantService = $tenantService;
        $this->planningService = $planningService;
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
    public function getSubscriptions(Fund $fund): Collection
    {
        return $fund->subscriptions()->with(['payable', 'member'])->get()
            ->filter(function($subscription) {
                return !$subscription->payable->session_id;
            })->pluck('member.name', 'id');
    }

    /**
     * Get a list of members for the dropdown select component.
     *
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->tenantService->tontine()->members()
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Find a member.
     *
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(int $memberId): ?Member
    {
        return $this->tenantService->tontine()->members()->find($memberId);
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
        $biddings = $session->biddings()->with('member');
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
     * @param Session $session The session
     * @param Member $member The member
     * @param int $amountBid
     * @param int $amountPaid
     *
     * @return void
     */
    public function createBidding(Session $session, Member $member, int $amountBid, int $amountPaid): void
    {
        $bidding = new Bidding();
        $bidding->amount_bid = $amountBid;
        $bidding->amount_paid = $amountPaid;
        $bidding->member()->associate($member);
        $bidding->session()->associate($session);
        $bidding->save();
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
     * @param Session $session
     *
     * @return Collection
     */
    private function getSessionPayables(Session $session): Collection
    {
        $sessions = $this->tenantService->round()->sessions;
        return $session->payables->map(function($payable) use($sessions) {
            $payable->amount = $sessions->filter(function($session) use($payable) {
                return $session->enabled($payable->subscription->fund);
            })->count() * $payable->subscription->fund->amount;
            return $payable;
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
        // The amount available for bidding is the sum of the amounts paid for remittances,
        // and the amounts paid in the biddings.
        return Remittance::whereIn('payable_id',
            $session->payables()->pluck('id'))->sum('amount_paid') +
            $session->biddings->reduce(function($sum, $bidding) {
                return $sum + $bidding->amount_paid - $bidding->amount_bid;
            }, 0);
    }

    /**
     * Get all the cash biddings of a given session
     *
     * @param Session $session
     * @return void
     */
    public function getSessionBiddings(Session $session)
    {
        $payables = $this->getSessionPayables($session);
        $fundBiddings = $payables->map(function($payable) {
            return (object)[
                'id' => 0, // $payable->subscription->id,
                'title' => $payable->subscription->member->name,
                'amount' => Currency::format($payable->amount),
                'paid' => Currency::format($payable->remittance->amount_paid),
                'available' => false,
            ];
        });
        $cashBiddings = $this->getBiddings($session)->map(function($bidding) {
            return (object)[
                'id' => $bidding->id,
                'title' => $bidding->member->name,
                'amount' => Currency::format($bidding->amount_bid),
                'paid' => Currency::format($bidding->amount_paid),
                'available' => false,
            ];
        });
        // One opened bid for the amount already paid for the others bids.
        $biddings = collect([]);
        $amountAvailable = $this->getAmountAvailable($session);
        if($amountAvailable > 0)
        {
            $biddings->push((object)[
                'id' => 0,
                'title' => '__',
                'amount' => Currency::format($amountAvailable),
                'paid' => 0,
                'available' => true,
            ]);
        }

        return $biddings->merge($fundBiddings)->merge($cashBiddings);
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

    /**
     * @param Fund $fund
     * @param int $sessionId
     *
     * @return array|stdClass
     */
    public function getRemittanceFigures(Fund $fund, int $sessionId = 0)
    {
        return $this->planningService->getRemittanceFigures($fund, $sessionId);
    }
}
