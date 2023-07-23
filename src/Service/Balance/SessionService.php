<?php

namespace Siak\Tontine\Service\Balance;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Planning\PoolService;

class SessionService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService, PoolService $poolService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->poolService = $poolService;
    }

    /**
     * @param Pool $pool
     * @param Session $session
     * @param int $sessionCount
     *
     * @return string
     */
    private function getPoolAmountPaid(Pool $pool, Session $session, int $sessionCount = 1): string
    {
        if($this->tenantService->tontine()->is_libre)
        {
            $remitmentAmount = $this->poolService->getLibrePoolAmount($pool, $session);
            return $this->localeService->formatMoney($remitmentAmount);
        }

        return $this->localeService->formatMoney($pool->amount * $pool->paid * $sessionCount);
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getReceivables(Session $session): Collection
    {
        return Pool::where('round_id', $session->round_id)
            ->withCount([
                'subscriptions as total' => function($query) use($session) {
                    $query->whereHas('receivables', function($query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                },
                'subscriptions as paid' => function($query) use($session) {
                    $query->whereHas('receivables', function($query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('deposit');
                    });
                },
            ])
            ->get()
            ->map(function($pool) use($session) {
                $pool->total = $this->localeService->formatMoney($pool->amount * $pool->total);
                $pool->paid = $this->getPoolAmountPaid($pool, $session);
                $pool->amount = $this->localeService->formatMoney($pool->amount);
                return $pool;
            });
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getPayables(Session $session): Collection
    {
        return Pool::where('round_id', $session->round_id)
            ->withCount([
                'subscriptions as total' => function($query) use($session) {
                    $query->whereHas('payable', function($query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                },
                'subscriptions as paid' => function($query) use($session) {
                    $query->whereHas('payable', function($query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('remitment');
                    });
                },
            ])
            ->get()
            ->map(function($pool) use($session) {
                $sessionCount = $this->poolService->enabledSessionCount($pool);
                $pool->total = $this->localeService->formatMoney($pool->amount * $pool->total * $sessionCount);
                $pool->paid = $this->getPoolAmountPaid($pool, $session, $sessionCount);
                $pool->amount = $this->localeService->formatMoney($pool->amount);
                return $pool;
            });
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getTontineFees(Session $session): Collection
    {
        return DB::table('bills')
            ->join('tontine_bills', 'bills.id', '=', 'tontine_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total'), 'tontine_bills.charge_id')
            ->groupBy('tontine_bills.charge_id')
            ->whereExists(function(Builder $query) use($session) {
                return $query->select(DB::raw(1))
                    ->from('settlements')
                    ->where('session_id', $session->id)
                    ->whereColumn('settlements.bill_id', 'bills.id');
            })
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getRoundFees(Session $session): Collection
    {
        return DB::table('bills')
            ->join('round_bills', 'bills.id', '=', 'round_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total'), 'round_bills.charge_id')
            ->groupBy('round_bills.charge_id')
            ->whereExists(function(Builder $query) use($session) {
                return $query->select(DB::raw(1))
                    ->from('settlements')
                    ->where('session_id', $session->id)
                    ->whereColumn('settlements.bill_id', 'bills.id');
            })
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getSessionFees(Session $session): Collection
    {
        return DB::table('bills')
            ->join('session_bills', 'bills.id', '=', 'session_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total'), 'session_bills.charge_id')
            ->groupBy('session_bills.charge_id')
            ->whereExists(function(Builder $query) use($session) {
                return $query->select(DB::raw(1))
                    ->from('settlements')
                    ->where('session_id', $session->id)
                    ->whereColumn('settlements.bill_id', 'bills.id');
            })
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getFees(Session $session): Collection
    {
        $bills = $this->getTontineFees($session)
            ->concat($this->getRoundFees($session))
            ->concat($this->getSessionFees($session))
            ->pluck('total', 'charge_id');
        return $this->tenantService->tontine()->charges()->fee()->get()
            ->map(function($fee) use($bills) {
                $fee->amount = $this->localeService->formatMoney($fee->amount);
                $fee->total = $this->localeService->formatMoney($bills[$fee->id] ?? 0);
                return $fee;
            });
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getFines(Session $session): Collection
    {
        $bills = DB::table('bills')
            ->join('fine_bills', 'bills.id', '=', 'fine_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total'), 'fine_bills.charge_id')
            ->groupBy('fine_bills.charge_id')
            ->whereExists(function(Builder $query) use($session) {
                return $query->select(DB::raw(1))
                    ->from('settlements')
                    ->where('session_id', $session->id)
                    ->whereColumn('settlements.bill_id', 'bills.id');
            })
            ->get()->pluck('total', 'charge_id');
        return $this->tenantService->tontine()->charges()->fine()->get()
            ->map(function($fine) use($bills) {
                $fine->amount = $this->localeService->formatMoney($fine->amount);
                $fine->total = $this->localeService->formatMoney($bills[$fine->id] ?? 0);
                return $fine;
            });
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getFunding(Session $session): object
    {
        $funding = DB::table('fundings')
            ->select(DB::raw('sum(amount) as amount'))
            ->where('session_id', $session->id)
            ->first();
        return (object)[
            'amount' => $this->localeService->formatMoney($funding->amount ?? 0),
        ];
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getLoan(Session $session): object
    {
        $loan = DB::table('loans')
            ->select(DB::raw('sum(amount) as amount'), DB::raw('sum(interest) as interest'))
            ->where('session_id', $session->id)
            ->first();
        return (object)[
            'amount' => $this->localeService->formatMoney($loan->amount ?? 0),
            'interest' => $this->localeService->formatMoney($loan->interest ?? 0),
        ];
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getRefund(Session $session): object
    {
        $amount = "CASE WHEN debts.type='" . Debt::TYPE_PRINCIPAL .
            "' THEN loans.amount ELSE 0 END";
        $interest = "CASE WHEN debts.type='" . Debt::TYPE_INTEREST .
            "' THEN loans.interest ELSE 0 END";
        $refund = DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->select(DB::raw("sum($amount) as amount"), DB::raw("sum($interest) as interest"))
            ->where('refunds.session_id', $session->id)
            ->first();
        return (object)[
            'amount' => $this->localeService->formatMoney($refund->amount ?? 0),
            'interest' => $this->localeService->formatMoney($refund->interest ?? 0),
        ];
    }
}
