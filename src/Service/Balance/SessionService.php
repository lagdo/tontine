<?php

namespace Siak\Tontine\Service\Balance;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Planning\PoolService;

class SessionService
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
     * @param Pool $pool
     * @param Session $session
     * @param int $sessionCount
     *
     * @return string
     */
    private function getPoolAmountPaid(Pool $pool, Session $session, int $sessionCount = 1): string
    {
        return $this->tenantService->tontine()->is_libre ?
            $this->poolService->getLibrePoolAmount($pool, $session) :
            $pool->amount * $pool->paid * $sessionCount;
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
            ->each(function($pool) use($session) {
                $pool->total = $pool->amount * $pool->total;
                $pool->paid = $this->getPoolAmountPaid($pool, $session);
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
            ->each(function($pool) use($session) {
                $sessionCount = $this->poolService->enabledSessionCount($pool);
                $pool->total = $pool->amount * $pool->total * $sessionCount;
                $pool->paid = $this->getPoolAmountPaid($pool, $session, $sessionCount);
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

        return $this->tenantService->tontine()->charges()->fixed()->get()
            ->each(function($fee) use($bills) {
                $fee->total = $bills[$fee->id] ?? 0;
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

        return $this->tenantService->tontine()->charges()->variable()->get()
            ->filter(function($fine) use($bills) {
                // Filter on fees with paid bills.
                return isset($bills[$fine->id]);
            })
            ->each(function($fine) use($bills) {
                $fine->total = $bills[$fine->id] ?? 0;
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
        if(!$funding->amount)
        {
            $funding->amount = 0;
        }

        return $funding;
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getLoan(Session $session): object
    {
        $principal = "CASE WHEN debts.type='" . Debt::TYPE_PRINCIPAL . "' THEN amount ELSE 0 END";
        $interest = "CASE WHEN debts.type='" . Debt::TYPE_INTEREST . "' THEN amount ELSE 0 END";
        $loan = DB::table('loans')
            ->join('debts', 'loans.id', '=', 'debts.loan_id')
            ->select(DB::raw("sum($principal) as principal"), DB::raw("sum($interest) as interest"))
            ->where('session_id', $session->id)
            ->first();
        if(!$loan->principal)
        {
            $loan->principal = 0;
        }
        if(!$loan->interest)
        {
            $loan->interest = 0;
        }

        return $loan;
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getRefund(Session $session): object
    {
        $principal = "CASE WHEN debts.type='" . Debt::TYPE_PRINCIPAL . "' THEN amount ELSE 0 END";
        $interest = "CASE WHEN debts.type='" . Debt::TYPE_INTEREST . "' THEN amount ELSE 0 END";
        $refund = DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->select(DB::raw("sum($principal) as principal"), DB::raw("sum($interest) as interest"))
            ->where('refunds.session_id', $session->id)
            ->first();
        if(!$refund->principal)
        {
            $refund->principal = 0;
        }
        if(!$refund->interest)
        {
            $refund->interest = 0;
        }

        return $refund;
    }
}
