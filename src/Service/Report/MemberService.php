<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\BillView;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\BalanceCalculator;

class MemberService
{
    /**
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private BalanceCalculator $balanceCalculator)
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
     * Get the deposits in the current session for receivables of different sessions.
     *
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getExtraDeposits(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName($session->deposits()
            ->whereHas('receivable', fn($qr) => $qr
                ->where('session_id', '!=', $session->id)
                ->when($member !== null, fn($qm) =>
                    $this->hasSubscription($qm, $member)))
            ->with(['receivable.session', 'receivable.subscription.pool',
                'receivable.subscription.member'])
            ->get()
            ->each(function($deposit) {
                $deposit->pool = $deposit->receivable->subscription->pool;
                $deposit->member = $deposit->receivable->subscription->member;
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
        // Order the bills by session date desc, then by member name asc.
        return BillView::query()
            ->select('v_bills.*')
            ->join('sessions', 'v_bills.session_id', '=', 'sessions.id')
            ->orderByDesc('sessions.day_date')
            ->when($member !== null, fn(Builder $query) =>
                $query->where('v_bills.member_id', $member->id))
            ->when($member === null, fn(Builder $query) =>
                $query->join('members', 'v_bills.member_id', '=', 'members.id')
                    ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
                    ->orderBy('member_defs.name'))
            ->where(fn(Builder $query) => $query
                ->orWhere(fn(Builder $q1) => $q1->ofTypeSession($session))
                ->orWhere(fn(Builder $q2) => $q2->ofTypeNotSession($session)))
            ->with(['round', 'session', 'member', 'charge', 'bill', 'bill.settlement'])
            ->get()
            ->each(function($bill) {
                $bill->paid = $bill->bill->settlement !== null;
                $bill->amount = $bill->bill->amount;
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
        return Debt::with(['loan.session', 'refund'])
            // Member debts
            ->whereHas('loan', fn(Builder $qm) =>
                $qm->where('member_id', $member->id))
            ->where(function($query) use($session) {
                // Take all the debts in the current session
                $query->where(fn($ql) =>
                    $ql->whereHas('loan', fn(Builder $qs) =>
                        $qs->where('session_id', $session->id)));
                // The debts in the previous sessions.
                $query->orWhere(function($query) use($session) {
                    $query->whereHas('loan', fn(Builder $qs) =>
                        $qs->whereHas('session', fn($qs) => $qs->precedes($session)))
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
    public function getTransfers(Session $session, ?Member $member = null): Collection
    {
        return $this->sortByMemberName($session->transfers()
            ->with(['member', 'session'])
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
