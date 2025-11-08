<?php

namespace Siak\Tontine\Service\Report;

use Closure;
use Illuminate\Database\Eloquent\Builder as ElBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Deposit;
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
            ->addSelect([
                'paid_amount' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->whereHas('receivable', fn(ElBuilder $qr) =>
                        $qr->whereSession($session)),
            ])
            ->withCount([
                'receivables as total_count' => fn(ElBuilder $query) =>
                    $query->whereSession($session),
                'receivables as paid_count' => fn(ElBuilder $query) =>
                    $query->whereSession($session)->paidHere($session),
                'receivables as late_count' => fn(ElBuilder $query) =>
                    $query->whereSession($session)->paidLater($session),
            ])
            ->get()
            ->each(function($pool) {
                $pool->paid_amount ??= 0;
                $pool->total_amount = $pool->amount * $pool->total_count;
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
                'payables as total_count' => fn(ElBuilder $query) =>
                    $query->whereSession($session),
                'payables as paid_count' => fn(ElBuilder $query) =>
                    $query->whereSession($session)->whereHas('remitment'),
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
    private function getSettlements(Closure $settlementFilter, ?Member $member = null): Collection
    {
        return DB::table('v_settlements')
            ->select(DB::raw('sum(amount) as total_amount'),
                DB::raw('count(*) as total_count'), 'charge_id')
            ->groupBy('charge_id')
            ->where($settlementFilter)
            ->when($member !== null, fn($qm) =>
                $qm->where('member_id', $member->id))
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
        $settlementFilter = fn(Builder $qs) =>
            $qs->where('session_id', $session->id);
        $settlements = $this->getSettlements($settlementFilter);
        $outflows = $this->getDisbursedAmounts($session, true);

        return $session->round->charges
            ->map(function($charge) use($settlements, $outflows) {
                $settlement = $settlements[$charge->id] ?? null;
                // We need to clone the model, or else the getTotalCharges()
                // method will modify and return the same object and values.
                $clone = $charge->replicateQuietly();
                $clone->id = $charge->id; // The replicate() function doesn't copy the id.
                $clone->total_count = $settlement?->total_count ?? 0;
                $clone->total_amount = $settlement?->total_amount ?? 0;
                $clone->outflow = $outflows[$charge->id] ?? null;
                return $clone;
            });
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getTotalCharges(Session $session, ?Member $member = null): Collection
    {
        $settlementFilter = fn(Builder $qs) =>
            $qs->whereIn('session_id', DB::table('sessions')
                ->select('id')
                ->where('round_id', $session->round_id)
                ->where('day_date', '<=', $session->day_date));
        $settlements = $this->getSettlements($settlementFilter, $member);
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
            ->map(function($charge) use($settlements, $outflows) {
                $settlement = $settlements[$charge->id] ?? null;
                // We need to clone the model, or else the getSessionCharges()
                // method will modify and return the same object and values.
                $clone = $charge->replicateQuietly();
                $clone->id = $charge->id; // The replicate() function doesn't copy the id.
                $clone->total_count = $settlement?->total_count ?? 0;
                $clone->total_amount = $settlement?->total_amount ?? 0;
                $clone->outflow = $outflows[$charge->id] ?? null;
                return $clone;
            });
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
    public function getTransfer(Session $session): object
    {
        $transfer = DB::table('settlements')
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(settlements.id) as total_count'))
            ->where('settlements.session_id', $session->id)
            ->whereNotNull('settlements.fund_id')
            ->first();
        if(!$transfer->total_amount)
        {
            $transfer->total_amount = 0;
        }
        if(!$transfer->total_count)
        {
            $transfer->total_count = 0;
        }

        return $transfer;
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
