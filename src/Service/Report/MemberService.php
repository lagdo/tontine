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
use Siak\Tontine\Model\Subscription;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Planning\PoolService;

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
     * @return string
     */
    private function getReceivableAmount(Receivable $receivable): string
    {
        if($this->tenantService->tontine()->is_libre)
        {
            return !$receivable->deposit ? 0 : $receivable->deposit->amount;
        }

        return $receivable->subscription->pool->amount;
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getReceivables(Session $session, Member $member): Collection
    {
        return $session->receivables()
            ->whereHas('subscription', function(Builder $query) use($member) {
                $query->where('member_id', $member->id);
            })
            ->with(['deposit', 'subscription.pool'])
            ->get()
            ->each(function($receivable) {
                $receivable->paid = ($receivable->deposit !== null);
                $receivable->amount = $this->getReceivableAmount($receivable);
            });
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return string
     */
    private function getPayableAmount(Pool $pool, Session $session): string
    {
        return $this->tenantService->tontine()->is_libre ?
            $this->poolService->getLibrePoolAmount($pool, $session) :
            $pool->amount * $this->poolService->enabledSessionCount($pool);
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getPayables(Session $session, Member $member): Collection
    {
        return Subscription::where('member_id', $member->id)
            ->whereHas('payable', function($query) use($session) {
                $query->where('session_id', $session->id);
            })
            ->with([
                'pool',
                'payable' => function($query) use($session) {
                    $query->where('session_id', $session->id)->whereHas('remitment');
                },
            ])
            ->get()
            ->each(function($subscription) use($session) {
                $subscription->paid = ($subscription->payable !== null);
                $subscription->amount = $this->getPayableAmount($subscription->pool, $session);
            });
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getFees(Session $session, Member $member): Collection
    {
        return Bill::with(['settlement'])
            // Bills settled on this session.
            ->whereHas('settlement', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            })
            // Member bills.
            ->where(function($query) use($member, $session) {
                return $query
                    // Tontine bills.
                    ->orWhere(function(Builder $query) use($member) {
                        return $query->whereHas('tontine_bill', function(Builder $query) use($member) {
                            return $query->where('member_id', $member->id);
                        });
                    })
                    // Round bills.
                    ->orWhere(function(Builder $query) use($member, $session) {
                        return $query->whereHas('round_bill', function(Builder $query) use($member, $session) {
                            return $query->where('member_id', $member->id)
                                ->where('round_id', $session->round_id);
                        });
                    })
                    // Session bills.
                    ->orWhere(function(Builder $query) use($member, $session) {
                        return $query->whereHas('session_bill', function(Builder $query) use($member, $session) {
                            return $query->where('member_id', $member->id)
                                ->where('session_id', $session->id);
                        });
                    });
            })
            ->get()
            ->each(function($bill) {
                $bill->paid = $bill->settlement !== null;
                $bill->session = $bill->session_bill ? $bill->session_bill->session : null;
            });
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getFines(Session $session, Member $member): Collection
    {
        return Bill::with(['settlement', 'fine_bill.session'])
            ->whereHas('fine_bill', function(Builder $query) use($member) {
                return $query->where('member_id', $member->id);
            })
            ->where(function($query) use($session) {
                return $query
                    // Bills settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('settlement', function(Builder $query) use($session) {
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
                $bill->session = $bill->fine_bill->session;
            });
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getLoans(Session $session, Member $member): Collection
    {
        return $session->loans()->with(['session'])->where('member_id', $member->id)->get();
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getDebts(Session $session, Member $member): Collection
    {
        return Debt::with(['loan', 'loan.session'])
            // Member debts
            ->whereHas('loan', function(Builder $query) use($member) {
                $query->where('member_id', $member->id);
            })
            // Debts refunded on this session.
            ->whereHas('refund', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            })
            ->get();
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getFundings(Session $session, Member $member): Collection
    {
        return $session->fundings()->with(['session'])->where('member_id', $member->id)->get();
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getDisbursements(Session $session, Member $member): Collection
    {
        return $session->disbursements()->with(['session', 'category'])
            ->where('member_id', $member->id)->get();
    }
}
