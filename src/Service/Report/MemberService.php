<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;

class MemberService
{
    /**
     * @param BalanceCalculator $balanceCalculator
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     */
    public function __construct(private BalanceCalculator $balanceCalculator,
        private TenantService $tenantService, private SessionService $sessionService)
    {}

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
                $pool = $payable->subscription->pool;
                $payable->pool = $pool;
                $payable->member = $payable->subscription->member;
                $payable->paid = ($payable->remitment !== null);
                $payable->amount = $this->balanceCalculator->getPayableAmount($pool, $session);
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
                                return $query->whereHas('member', function($query) {
                                    $tontine = $this->tenantService->tontine();
                                    return $query->where('tontine_id', $tontine->id);
                                });
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
                        $sessionIds = $this->sessionService->getRoundSessionIds($session);
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
    public function getRefunds(Session $session, ?Member $member = null): Collection
    {
        $refunds = $session->refunds()->select('refunds.*')
            ->with(['debt.loan.session', 'debt.loan.member'])
            // Member refunds
            ->when($member !== null, function(Builder $query) use($member) {
                return $query->join('debts', 'refunds.debt_id', '=', 'debts.id')
                    ->join('loans', 'debts.loan_id', '=', 'loans.id')
                    ->where('loans.member_id', $member->id);
            })
            ->get()
            ->each(function($refund) {
                $refund->amount = $refund->debt->amount;
                $refund->debt->session = $refund->debt->loan->session;
                $refund->member = $refund->debt->loan->member;
                $refund->is_partial = false;
            })
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
        $partialRefunds = $session->partial_refunds()->select('partial_refunds.*')
            ->with(['debt.loan.session', 'debt.loan.member'])
            // Member refunds
            ->when($member !== null, function(Builder $query) use($member) {
                return $query->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
                    ->join('loans', 'debts.loan_id', '=', 'loans.id')
                    ->where('loans.member_id', $member->id);
            })
            ->get()
            ->each(function($refund) {
                $refund->debt->session = $refund->debt->loan->session;
                $refund->member = $refund->debt->loan->member;
                $refund->is_partial = true;
            })
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });

        return $refunds->concat($partialRefunds);
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getSavings(Session $session, ?Member $member = null): Collection
    {
        return $session->savings()->with(['member'])
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
