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
use Siak\Tontine\Service\Meeting\Session\SessionService;
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
     * @param Collection $collection
     * @param Member|null $member
     *
     * @return Collection
     */
    private function sortByMemberName(Collection $collection, ?Member $member): Collection
    {
        return $member !== null ? $collection :
            $collection->sortBy('member.name', SORT_LOCALE_STRING)->values();
    }

    /**
     * @param Builder $query
     * @param Member $member
     *
     * @return Builder
     */
    private function hasSubscription(Builder $query, Member $member): Builder
    {
        return $query->whereHas('subscription', fn(Builder $qm) =>
            $qm->where('member_id', $member->id));
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getReceivables(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName($session->receivables()
            ->when($member !== null, fn($qw) =>
                $this->hasSubscription($qw, $member))
            ->with(['deposit', 'subscription.pool', 'subscription.member'])
            ->get()
            ->each(function($receivable) {
                $receivable->pool = $receivable->subscription->pool;
                $receivable->member = $receivable->subscription->member;
                $receivable->paid = $receivable->deposit !== null;
                $receivable->amount = $this->balanceCalculator->getReceivableAmount($receivable);
            }), $member);
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getPayables(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName($session->payables()
            ->when($member !== null, fn($qw) =>
                $this->hasSubscription($qw, $member))
            ->with(['remitment', 'subscription.pool', 'subscription.member'])
            ->get()
            ->each(function($payable) use($session) {
                $pool = $payable->subscription->pool;
                $payable->pool = $pool;
                $payable->member = $payable->subscription->member;
                $payable->paid = $payable->remitment !== null;
                $payable->amount = $this->balanceCalculator->getPayableAmount($pool, $session);
            }), $member);
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getAuctions(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName($session->auctions()
            ->when($member !== null, fn($qm) =>
                $qm->whereHas('remitment', fn($qr) =>
                    $qr->whereHas('payable', fn($qp) =>
                        $this->hasSubscription($qp, $member))))
            ->with(['remitment.payable.subscription.pool',
                'remitment.payable.subscription.member'])
            ->get()
            ->each(function(Auction $auction) {
                $subscription = $auction->remitment->payable->subscription;
                $auction->member = $subscription->member;
                $auction->pool = $subscription->pool;
            })
            ->keyBy('remitment.payable.id'), $member);
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getBills(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName(Bill::with(['settlement',
                'libre_bill.session', 'libre_bill.member', 'round_bill.member',
                'oneoff_bill.member', 'session_bill.member', 'session_bill.session'])
            ->where(fn($query) => $query
                // Unsettled bills.
                ->orWhereDoesntHave('settlement')
                // Bills settled on this session.
                ->orWhereHas('settlement', fn(Builder $qs) =>
                    $qs->where('session_id', $session->id)))
            ->where(fn($query) => $query
                // Oneoff bills.
                ->orWhereHas('oneoff_bill', fn(Builder $qb) =>
                    $qb->when($member !== null, fn($qm) =>
                            $qm->where('member_id', $member->id))
                        ->when($member === null, fn($qw) =>
                            $qw->whereHas('member', fn($qm) =>
                                $qm->where('round_id', $session->round_id))))
                // Round bills.
                ->orWhereHas('round_bill', fn(Builder $qb) =>
                    $qb->where('round_id', $session->round_id)
                        ->when($member !== null, fn($qm) =>
                            $qm->where('member_id', $member->id)))
                // Session bills.
                ->orWhereHas('session_bill', fn(Builder $qb) =>
                    $qb->where('session_id', $session->id)
                        ->when($member !== null, fn($qm) =>
                            $qm->where('member_id', $member->id)))
                // Libre bills, all up to this session.
                ->orWhereHas('libre_bill', fn(Builder $qb) =>
                    $qb->whereHas('session', fn($qs) =>
                            $qs->where('round_id', $session->round_id)
                                ->where('day_date', '<=', $session->day_date))
                        ->when($member !== null, fn($qm) =>
                            $qm->where('member_id', $member->id)))
            )
            ->get()
            ->each(function($bill) {
                // Take the only value which is not null
                $_bill = $bill->session_bill ?? $bill->round_bill ??
                    $bill->oneoff_bill ?? $bill->libre_bill;

                $bill->paid = $bill->settlement !== null;
                $bill->session = $bill->libre_bill ? $_bill->session : null;
                $bill->member = $_bill->member;
                $bill->charge_id = $_bill->charge_id;
            }), $member);
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getLoans(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName($session->loans()
            ->when($member !== null, fn($qm) =>
                $qm->where('member_id', $member->id))
            ->with(['member', 'principal_debt.refund', 'interest_debt.refund'])
            ->get(), $member);
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return Collection
     */
    public function getDebts(Session $session, Member $member): Collection
    {
        $prevSessions = $this->sessionService->getSessionIds($session->round, $session);
        return Debt::with(['loan.session', 'refund'])
            // Member debts
            ->whereHas('loan', fn(Builder $qm) =>
                $qm->where('member_id', $member->id))
            ->where(function($query) use($session, $prevSessions) {
                // Take all the debts in the current session
                $query->where(fn($ql) =>
                    $ql->whereHas('loan', fn(Builder $qs) =>
                        $qs->where('session_id', $session->id)));
                if($prevSessions->count() === 0)
                {
                    return;
                }

                // The debts in the previous sessions.
                $query->orWhere(function($query) use($session, $prevSessions) {
                    $query->whereHas('loan', fn(Builder $qs) =>
                        $qs->whereIn('session_id', $prevSessions))
                    ->where(function($query) use($session) {
                        // The debts that are not yet refunded.
                        $query->orWhereDoesntHave('refund');
                        // The debts that are refunded in the current session.
                        $query->orWhereHas('refund', fn(Builder $qs) =>
                            $qs->where('session_id', $session->id));
                    });
                });
            })
            ->get()
            ->each(function($debt) {
                $debt->paid = $debt->refund !== null;
                $debt->session = $debt->loan->session;
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
        $refunds = $this->sortByMemberName($session->refunds()->select('refunds.*')
            ->with(['debt.loan.session', 'debt.loan.member'])
            // Member refunds
            ->when($member !== null, fn(Builder $query) => $query
                ->join('debts', 'refunds.debt_id', '=', 'debts.id')
                ->join('loans', 'debts.loan_id', '=', 'loans.id')
                ->where('loans.member_id', $member->id))
            ->get()
            ->each(function($refund) {
                $refund->amount = $refund->debt->amount;
                $refund->debt->session = $refund->debt->loan->session;
                $refund->member = $refund->debt->loan->member;
                $refund->is_partial = false;
            }), $member);
        $partialRefunds = $this->sortByMemberName($session->partial_refunds()
            ->select('partial_refunds.*')
            ->with(['debt.loan.session', 'debt.loan.member'])
            // Member refunds
            ->when($member !== null, fn(Builder $query) => $query
                ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
                ->join('loans', 'debts.loan_id', '=', 'loans.id')
                ->where('loans.member_id', $member->id))
            ->get()
            ->each(function($refund) {
                $refund->debt->session = $refund->debt->loan->session;
                $refund->member = $refund->debt->loan->member;
                $refund->is_partial = true;
            }), $member);

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
        return $this->sortByMemberName($session->savings()
            ->with(['member'])
            ->when($member !== null, fn($query) => $query->where('member_id', $member->id))
            ->get(), $member);
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getOutflows(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName($session->outflows()
            ->with(['member', 'category'])
            ->when($member !== null, fn($query) => $query->where('member_id', $member->id))
            ->get(), $member);
    }
}
