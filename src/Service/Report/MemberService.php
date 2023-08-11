<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Planning\PoolService;
use Intl;

class MemberService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(TenantService $tenantService, PoolService $poolService)
    {
        $this->tenantService = $tenantService;
        $this->poolService = $poolService;
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
     * @param Receivable $receivable
     *
     * @return int
     */
    private function getReceivableAmount(Receivable $receivable): int
    {
        if($this->tenantService->tontine()->is_libre)
        {
            return !$receivable->deposit ? 0 : $receivable->deposit->amount;
        }

        return $receivable->subscription->pool->amount;
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
                $receivable->amount = $this->getReceivableAmount($receivable);
            })
            // Sort by member name
            ->when($member === null, function($collection) {
                return $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
            });
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    private function getPayableAmount(Pool $pool, Session $session): int
    {
        return $this->tenantService->tontine()->is_libre ?
            $this->poolService->getLibrePoolAmount($pool, $session) :
            $pool->amount * $this->poolService->enabledSessionCount($pool);
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
                $payable->amount = $this->getPayableAmount($payable->pool, $session);
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
    public function getFees(Session $session, ?Member $member = null): Collection
    {
        return Bill::with(['round_bill.member', 'session_bill.member', 'tontine_bill.member'])
            // Bills settled on this session.
            ->whereHas('settlement', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            })
            // Member bills.
            ->where(function($query) use($member, $session) {
                return $query
                    // Round bills.
                    ->orWhere(function(Builder $query) use($member, $session) {
                        return $query->whereHas('round_bill', function(Builder $query) use($member, $session) {
                            return $query->where('round_id', $session->round_id)
                                ->when($member !== null, function($query) use($member) {
                                    return $query->where('member_id', $member->id);
                                });
                        });
                    })
                    // Session bills.
                    ->orWhere(function(Builder $query) use($member, $session) {
                        return $query->whereHas('session_bill', function(Builder $query) use($member, $session) {
                            return $query->where('session_id', $session->id)
                                ->when($member !== null, function($query) use($member) {
                                    return $query->where('member_id', $member->id);
                                });
                        });
                    })
                    // Tontine bills.
                    ->when($member !== null, function($query) use($member) {
                        return $query->orWhere(function(Builder $query) use($member) {
                            return $query->whereHas('tontine_bill', function(Builder $query) use($member) {
                                return $query->where('member_id', $member->id);
                            });
                        });
                    });
            })
            ->get()
            ->each(function($bill) {
                $bill->paid = true; // $bill->settlement !== null;
                $bill->session = $bill->session_bill ? $bill->session_bill->session : null;
                $bill->member = $bill->session_bill ? $bill->session_bill->member :
                    ($bill->round_bill ? $bill->round_bill->member : $bill->tontine_bill->member);
                $bill->charge_id = $bill->session_bill ? $bill->session_bill->charge_id :
                    ($bill->round_bill ? $bill->round_bill->charge_id : $bill->tontine_bill->charge_id);
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
    public function getFines(Session $session, ?Member $member = null): Collection
    {
        return Bill::with(['settlement', 'fine_bill.session', 'fine_bill.member'])
            ->when($member !== null, function($query) use($member) {
                return $query->whereHas('fine_bill', function(Builder $query) use($member) {
                    return $query->where('member_id', $member->id);
                });
            })
            ->where(function($query) use($session) {
                return $query
                    // Bills settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('fine_bill')
                            ->whereHas('settlement', function(Builder $query) use($session) {
                                $query->where('session_id', $session->id);
                            });
                    })
                    // Bills created on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('fine_bill', function(Builder $query) use($session) {
                            return $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get()
            ->each(function($bill) {
                $bill->paid = $bill->settlement !== null;
                $bill->session = $bill->fine_bill->session;
                $bill->member = $bill->fine_bill->member;
                $bill->charge_id = $bill->fine_bill->charge_id;
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
        return $session->loans()->with(['member', 'principal_debt', 'interest_debt'])
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
