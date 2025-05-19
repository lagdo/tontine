<?php

namespace Siak\Tontine\Service\Report;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Outflow;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\BalanceCalculator;

class SessionService
{
    use Traits\Queries;

    /**
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private BalanceCalculator $balanceCalculator)
    {}

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getReceivables(Session $session): Collection
    {
        return $session->pools()
            ->withCount([
                'subscriptions as total_count' => function($query) use($session) {
                    $query->whereHas('receivables', function($query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                },
                'subscriptions as paid_count' => function($query) use($session) {
                    $query->whereHas('receivables', function($query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('deposit');
                    });
                },
            ])
            ->get()
            ->each(function($pool) use($session) {
                $pool->total_amount = $pool->amount * $pool->total_count;
                $pool->paid_amount = $this->balanceCalculator->getPoolDepositAmount($pool, $session);
            });
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getPayables(Session $session): Collection
    {
        return $session->pools()
            ->withCount([
                'sessions',
                'subscriptions as total_count' => function($query) use($session) {
                    $query->whereHas('payable', function($query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                },
                'subscriptions as paid_count' => function($query) use($session) {
                    $query->whereHas('payable', function($query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('remitment');
                    });
                },
            ])
            ->get()
            ->each(function($pool) use($session) {
                $pool->total_amount = $pool->amount * $pool->total_count * $pool->sessions_count;
                $pool->paid_amount = $this->balanceCalculator->getPoolRemitmentAmount($pool, $session);
            });
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getAuctions(Session $session): Collection
    {
        return DB::table('auctions')
            ->select(DB::raw('sum(auctions.amount) as total'), 'subscriptions.pool_id')
            ->join('remitments', 'auctions.remitment_id', '=', 'remitments.id')
            ->join('payables', 'remitments.payable_id', '=', 'payables.id')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->where('auctions.session_id', $session->id)
            ->where('paid', true)
            ->groupBy('subscriptions.pool_id')
            ->pluck('total', 'pool_id');
    }

    /**
     * @param Closure $settlementFilter
     * @param Member $member|null
     *
     * @return Collection
     */
    private function getBills(Closure $settlementFilter, ?Member $member = null): Collection
    {
        $onetimeBillsQuery = DB::table('bills')
            ->join(DB::raw('onetime_bills as ob'), 'bills.id', '=', 'ob.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'ob.charge_id')
            ->groupBy('ob.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, fn($qm) =>
                $qm->where('ob.member_id', $member->id));
        $roundBillsQuery = DB::table('bills')
            ->join(DB::raw('round_bills as rb'), 'bills.id', '=', 'rb.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'rb.charge_id')
            ->groupBy('rb.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, fn($qm) =>
                $qm->where('rb.member_id', $member->id));
        $sessionBillsQuery = DB::table('bills')
            ->join(DB::raw('session_bills as sb'), 'bills.id', '=', 'sb.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'sb.charge_id')
            ->groupBy('sb.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, fn($qm) =>
                $qm->where('sb.member_id', $member->id));
        $libreBillsQuery = DB::table('bills')
            ->join(DB::raw('libre_bills as lb'), 'bills.id', '=', 'lb.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'lb.charge_id')
            ->groupBy('lb.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, fn($qm) =>
                $qm->where('lb.member_id', $member->id));

        return $onetimeBillsQuery
            ->union($roundBillsQuery)
            ->union($sessionBillsQuery)
            ->union($libreBillsQuery)
            ->get()
            ->keyBy('charge_id');
    }

    /**
     * @param Session $session
     * @param bool $onlyCurrent
     *
     * @return Collection
     */
    private function getDisbursedAmounts(Session $session, bool $onlyCurrent): Collection
    {
        return Outflow::select(DB::raw('sum(amount) as total_amount'),
                DB::raw('count(*) as total_count'), 'charge_id')
            ->groupBy('charge_id')
            ->whereHas('charge', fn($qc) => $qc->where('round_id', $session->round_id))
            ->when($onlyCurrent, fn($qo) => $qo->where('session_id', $session->id))
            ->when(!$onlyCurrent, fn($qo) =>
                $qo->whereHas('session', fn($qs) => $qs->precedes($session)))
            ->get()
            ->keyBy('charge_id');
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionCharges(Session $session): Collection
    {
        $settlementFilter = fn(Builder $query) => $query
            ->select(DB::raw(1))
            ->from(DB::raw('settlements as st'))
            ->where('session_id', $session->id)
            ->whereColumn('st.bill_id', 'bills.id');
        $bills = $this->getBills($settlementFilter);
        $outflows = $this->getDisbursedAmounts($session, true);

        return $session->round->charges
            ->each(function($charge) use($bills, $outflows) {
                $bill = $bills[$charge->id] ?? null;
                $charge->total_count = $bill ? $bill->total_count : 0;
                $charge->total_amount = $bill ? $bill->total_amount : 0;
                $charge->outflow = $outflows[$charge->id] ?? null;
            })
            ->filter(fn($charge) => $charge->total_count > 0 ||
                ($charge->outflow !== null && $charge->outflow->total_count > 0));
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getTotalCharges(Session $session, ?Member $member = null): Collection
    {
        $sessionFilter = fn(Builder $query) => $query
            ->select(DB::raw(1))
            ->from(DB::raw('sessions as se'))
            ->whereColumn('se.id', 'st.session_id')
            ->where('se.round_id', $session->round_id)
            ->where('se.day_date', '<=', $session->day_date);
        $settlementFilter = fn(Builder $query) => $query
            ->select(DB::raw(1))
            ->from(DB::raw('settlements as st'))
            ->whereExists($sessionFilter)
            ->whereColumn('st.bill_id', 'bills.id');
        $bills = $this->getBills($settlementFilter, $member);
        $outflows = $this->getDisbursedAmounts($session, false);
        if($member !== null)
        {
            // The outflow part of each member id calculated by dividing each amount
            // by the number of members.
            $memberCount = $session->round->members->count();
            foreach($outflows as $outflow)
            {
                $outflow->total_amount /= $memberCount;
            }
        }

        return $session->round->charges
            ->each(function($charge) use($bills, $outflows) {
                $bill = $bills[$charge->id] ?? null;
                $charge->total_count = $bill ? $bill->total_count : 0;
                $charge->total_amount = $bill ? $bill->total_amount : 0;
                $charge->outflow = $outflows[$charge->id] ?? null;
            })
            ->filter(fn($charge) => $charge->total_count > 0 ||
                ($charge->outflow !== null && $charge->outflow->total_count > 0));
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getLoan(Session $session): object
    {
        $loan = DB::table('loans')
            ->join('debts', 'loans.id', '=', 'debts.loan_id')
            ->select('debts.type', DB::raw('sum(debts.amount) as total_amount'))
            ->where('loans.session_id', $session->id)
            ->groupBy('debts.type')
            ->pluck('total_amount', 'type');

        return (object)[
            'principal' => $loan[Debt::TYPE_PRINCIPAL] ?? 0,
            'interest' => $loan[Debt::TYPE_INTEREST] ?? 0,
        ];
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getRefund(Session $session): object
    {
        $refund = $this->getRefundQuery()
            ->addSelect('debts.type')
            ->where('refunds.session_id', $session->id)
            ->groupBy('debts.type')
            ->pluck('total_amount', 'type');
        $partialRefund = DB::table('partial_refunds')
            ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
            ->where('partial_refunds.session_id', $session->id)
            ->select('debts.type', DB::raw('sum(partial_refunds.amount) as total_amount'))
            ->groupBy('debts.type')
            ->pluck('total_amount', 'type');

        return (object)[
            'principal' => ($refund[Debt::TYPE_PRINCIPAL] ?? 0) +
                ($partialRefund[Debt::TYPE_PRINCIPAL] ?? 0),
            'interest' => ($refund[Debt::TYPE_INTEREST] ?? 0) +
                ($partialRefund[Debt::TYPE_INTEREST] ?? 0),
        ];
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getSaving(Session $session): object
    {
        $saving = DB::table('savings')
            ->select(DB::raw('sum(amount) as total_amount'),
                DB::raw('count(id) as total_count'))
            ->where('session_id', $session->id)
            ->first();
        if(!$saving->total_amount)
        {
            $saving->total_amount = 0;
        }
        if(!$saving->total_count)
        {
            $saving->total_count = 0;
        }

        return $saving;
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getOutflow(Session $session): object
    {
        $outflow = DB::table('outflows')
            ->select(DB::raw('sum(amount) as total_amount'),
                DB::raw('count(id) as total_count'))
            ->where('session_id', $session->id)
            ->first();
        if(!$outflow->total_amount)
        {
            $outflow->total_amount = 0;
        }
        if(!$outflow->total_count)
        {
            $outflow->total_count = 0;
        }

        return $outflow;
    }
}
