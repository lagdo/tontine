<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\TenantService;

class MemberService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var BalanceCalculator
     */
    protected BalanceCalculator $balanceCalculator;

    /**
     * @param TenantService $tenantService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(TenantService $tenantService, BalanceCalculator $balanceCalculator)
    {
        $this->tenantService = $tenantService;
        $this->balanceCalculator = $balanceCalculator;
    }

    /**
     * Get a paginated list of members.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(int $page = 0): Collection
    {
        return $this->tenantService->tontine()->members()->active()
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('name', 'asc')->get();
    }

    /**
     * Get the number of members.
     *
     * @return int
     */
    public function getMemberCount(): int
    {
        return $this->tenantService->tontine()->members()->active()->count();
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getReceivables(Session $session, ?Member $member = null): Collection
    {
        return $session->receivables()
            ->when($member !== null, function($query) use($member) {
                return $query->whereHas('subscription', function(Builder $query) use($member) {
                    $query->where('member_id', $member->id);
                });
            })
            ->with(['deposit', 'subscription.pool', 'subscription.member'])
            ->get()
            ->each(function($receivable) {
                $receivable->pool = $receivable->subscription->pool;
                $receivable->member = $receivable->subscription->member;
                $receivable->paid = ($receivable->deposit !== null);
                $receivable->amount = $this->balanceCalculator->getReceivableAmount($receivable);
            })
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getPayables(Session $session, ?Member $member = null): Collection
    {
        return $session->payables()
            ->when($member !== null, function($query) use($member) {
                return $query->whereHas('subscription', function(Builder $query) use($member) {
                    $query->where('member_id', $member->id);
                });
            })
            ->with(['remitment', 'subscription.pool', 'subscription.member'])
            ->get()
            ->each(function($payable) use($session) {
                $payable->pool = $payable->subscription->pool;
                $payable->member = $payable->subscription->member;
                $payable->paid = ($payable->remitment !== null);
                $payable->amount = $this->balanceCalculator->getPayableAmount($payable, $session);
            })
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getAuctions(Session $session, ?Member $member = null): Collection
    {
        return $session->auctions()
            ->when($member !== null, function($query) use($member) {
                return $query->whereHas('remitment', function($query) use($member) {
                    $query->whereHas('payable', function($query) use($member) {
                        $query->whereHas('subscription', function($query) use($member) {
                            $query->where('member_id', $member->id);
                        });
                    });
                });
            })
            ->with(['remitment.payable.subscription.pool',
                'remitment.payable.subscription.member'])
            ->get()
            ->each(function(Auction $auction) {
                $subscription = $auction->remitment->payable->subscription;
                $auction->member = $subscription->member;
                $auction->pool = $subscription->pool;
            })
            ->keyBy('remitment.payable.id')
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getBills(Session $session, ?Member $member = null): Collection
    {
        return Bill::with(['settlement', 'libre_bill.session', 'libre_bill.member', 'round_bill.member',
                'tontine_bill.member', 'session_bill.member', 'session_bill.session'])
            ->where(function($query) use($session) {
                return $query
                    // Unsettled bills.
                    ->orWhereDoesntHave('settlement')
                    // Bills settled on this session.
                    ->orWhereHas('settlement', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id);
                    });
            })
            ->where(function($query) use($session, $member) {
                return $query
                    // Tontine bills.
                    ->orWhereHas('tontine_bill', function(Builder $query) use($member) {
                        return $query->when($member !== null, function($query) use($member) {
                                return $query->where('member_id', $member->id);
                            })
                            ->when($member === null, function($query) {
                                $memberIds = $this->tenantService->tontine()
                                    ->members()->active()->pluck('id');
                                return $query->whereIn('member_id', $memberIds);
                            });
                    })
                    // Round bills.
                    ->orWhereHas('round_bill', function(Builder $query) use($session, $member) {
                        return $query->where('round_id', $session->round_id)
                            ->when($member !== null, function($query) use($member) {
                                return $query->where('member_id', $member->id);
                            });
                    })
                    // Session bills.
                    ->orWhereHas('session_bill', function(Builder $query) use($session, $member) {
                        return $query->where('session_id', $session->id)
                            ->when($member !== null, function($query) use($member) {
                                return $query->where('member_id', $member->id);
                            });
                    })
                    // Libre bills, all up to this session.
                    ->orWhereHas('libre_bill', function(Builder $query) use($session, $member) {
                        $sessionIds = $this->tenantService->getSessionIds($session);
                        return $query->whereIn('session_id', $sessionIds)
                            ->when($member !== null, function($query) use($member) {
                                return $query->where('member_id', $member->id);
                            });
                    });
            })
            ->get()
            ->each(function($bill) {
                // Take the only value which is not null
                $_bill = $bill->session_bill ?? $bill->round_bill ??
                    $bill->tontine_bill ?? $bill->libre_bill;

                $bill->paid = $bill->settlement !== null;
                $bill->session = $bill->libre_bill ? $_bill->session : null;
                $bill->member = $_bill->member;
                $bill->charge_id = $_bill->charge_id;
            })
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getLoans(Session $session, ?Member $member = null): Collection
    {
        return $session->loans()
            ->when($member !== null, function($query) use($member) {
                return $query->where('member_id', $member->id);
            })
            ->with(['member', 'principal_debt.refund', 'interest_debt.refund'])
            ->get()
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getDebts(Session $session, ?Member $member = null): Collection
    {
        return Debt::with(['loan.session', 'loan.member'])
            // Member debts
            ->when($member !== null, function($query) use($member) {
                return $query->whereHas('loan', function(Builder $query) use($member) {
                    $query->where('member_id', $member->id);
                });
            })
            // Debts refunded on this session.
            ->whereHas('refund', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            })
            ->get()
            ->each(function($debt) {
                $debt->session = $debt->loan->session;
                $debt->member = $debt->loan->member;
            })
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getFundings(Session $session, ?Member $member = null): Collection
    {
        return $session->fundings()->with(['member'])
            ->when($member !== null, function($query) use($member) {
                return $query->where('member_id', $member->id);
            })
            ->get()
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getDisbursements(Session $session, ?Member $member = null): Collection
    {
        return $session->disbursements()->with(['member', 'category'])
            ->when($member !== null, function($query) use($member) {
                return $query->where('member_id', $member->id);
            })
            ->get()
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }
}
