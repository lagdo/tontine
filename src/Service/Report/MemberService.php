<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Funding;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Loan;
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
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getReceivables(Member $member, Session $session): Collection
    {
        return Receivable::where('session_id', $session->id)
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
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getPayables(Member $member, Session $session): Collection
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
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getFeeBills(Member $member, Session $session): Collection
    {
        $sessionIds = $this->tenantService->getPreviousSessions($session);
        return Bill::with(['settlement', 'tontine_bill', 'round_bill', 'session_bill', 'session_bill.session'])
            ->where(function($query) use($member, $session, $sessionIds) {
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
                    ->orWhere(function(Builder $query) use($member, $sessionIds) {
                        return $query->whereHas('session_bill', function(Builder $query) use($member, $sessionIds) {
                            return $query->where('member_id', $member->id)
                                ->whereIn('session_id', $sessionIds);
                        });
                    });
            })
            ->where(function($query) use($session) {
                return $query
                    // Bills that are not yet settled.
                    ->orWhere(function(Builder $query) {
                        return $query->whereDoesntHave('settlement');
                    })
                    // Bills settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('settlement', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get()
            ->each(function($bill) {
                $bill->session = $bill->session_bill ? $bill->session_bill->session : null;
            });
    }

        /**
         * @param Member $member
         * @param Session $session
         *
         * @return Collection
         */
        public function getFees(Member $member, Session $session): Collection
        {
            $bills = $this->getFeeBills($member, $session)
                ->each(function($bill) {
                    $bill->charge_id = $bill->session_bill ? $bill->session_bill->charge_id :
                        ($bill->round_bill ? $bill->round_bill->charge_id : $bill->tontine_bill->charge_id);
                })
                ->keyBy('charge_id');

            return $this->tenantService->tontine()->charges()->fixed()->get()
                ->each(function($charge) use($bills) {
                    $charge->paid = false;
                    $charge->session = null;
                    if(($bill = $bills->get($charge->id)) !== null)
                    {
                        $charge->paid = $bill->settlement !== null;
                        $charge->session = $bill->session;
                    }
                });
        }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getFineBills(Member $member, Session $session): Collection
    {
        $sessionIds = $this->tenantService->getPreviousSessions($session);
        return Bill::with(['settlement', 'fine_bill.session'])
            ->whereHas('fine_bill', function(Builder $query) use($member, $sessionIds) {
                return $query->where('member_id', $member->id)
                    ->whereIn('session_id', $sessionIds);
            })
            ->where(function($query) use($session) {
                return $query
                    // Bills that are not yet settled.
                    ->orWhere(function(Builder $query) {
                        return $query->whereDoesntHave('settlement');
                    })
                    // Bills settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('settlement', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get()
            ->each(function($bill) {
                $bill->session = $bill->fine_bill->session;
            });
        }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getFundings(Member $member, Session $session): Collection
    {
        return Funding::where('member_id', $member->id)
            ->where('session_id', $session->id)
            ->get();
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getLoans(Member $member, Session $session): Collection
    {
        return Loan::where('member_id', $member->id)
            ->where('session_id', $session->id)
            ->get();
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getDebts(Member $member, Session $session): Collection
    {
        $sessionIds = $this->tenantService->getPreviousSessions($session);
        return Debt::with(['refund', 'refund.session'])
            ->whereHas('loan', function(Builder $query) use($member, $sessionIds) {
                $query->where('member_id', $member->id)
                    ->whereIn('session_id', $sessionIds);
            })
            ->where(function($query) use($session) {
                return $query
                    // Loans at this session.
                    ->whereHas('loan', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id);
                    })
                    // Debts that are not yet refunded.
                    ->orWhere(function(Builder $query) {
                        return $query->whereDoesntHave('refund');
                    })
                    // Debts refunded on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('refund', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get();
    }
}
