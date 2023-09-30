<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\TenantService;

class SessionService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var BalanceCalculator
     */
    protected BalanceCalculator $balanceCalculator;

    /**
     * @param TenantService $tenantService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(TenantService $tenantService, BalanceCalculator $balanceCalculator)
    {
        $this->tenantService = $tenantService;
        $this->balanceCalculator = $balanceCalculator;
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
        return Pool::where('round_id', $session->round_id)
            ->withCount([
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
                $sessionCount = $this->balanceCalculator->enabledSessionCount($pool);
                $pool->total_amount = $pool->amount * $pool->total_count * $sessionCount;
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
     * @param Session $session
     *
     * @return Collection
     */
    private function getTontineFees(Session $session): Collection
    {
        return DB::table('bills')
            ->join('tontine_bills', 'bills.id', '=', 'tontine_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'tontine_bills.charge_id')
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
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'round_bills.charge_id')
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
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'session_bills.charge_id')
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
            ->keyBy('charge_id');

        return $this->tenantService->tontine()->charges()->fixed()->get()
            ->each(function($fee) use($bills) {
                $bill = $bills[$fee->id] ?? null;
                $fee->total_count = $bill ? $bill->total_count : 0;
                $fee->total_amount = $bill ? $bill->total_amount : 0;
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
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'fine_bills.charge_id')
            ->groupBy('fine_bills.charge_id')
            ->whereExists(function(Builder $query) use($session) {
                return $query->select(DB::raw(1))
                    ->from('settlements')
                    ->where('session_id', $session->id)
                    ->whereColumn('settlements.bill_id', 'bills.id');
            })
            ->get()->keyBy('charge_id');

        return $this->tenantService->tontine()->charges()->variable()->get()
            // ->filter(function($fine) use($bills) {
            //     // Filter on fees with paid bills.
            //     return isset($bills[$fine->id]);
            // })
            ->each(function($fine) use($bills) {
                $bill = $bills[$fine->id] ?? null;
                $fine->total_count = $bill ? $bill->total_count : 0;
                $fine->total_amount = $bill ? $bill->total_amount : 0;
            });
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
            ->where('loans.session_id', $session->id)
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

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getFunding(Session $session): object
    {
        $funding = DB::table('fundings')
            ->select(DB::raw('sum(amount) as total_amount'), DB::raw('count(id) as total_count'))
            ->where('session_id', $session->id)
            ->first();
        if(!$funding->total_amount)
        {
            $funding->total_amount = 0;
        }
        if(!$funding->total_count)
        {
            $funding->total_count = 0;
        }

        return $funding;
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getDisbursement(Session $session): object
    {
        $disbursement = DB::table('disbursements')
            ->select(DB::raw('sum(amount) as total_amount'), DB::raw('count(id) as total_count'))
            ->where('session_id', $session->id)
            ->first();
        if(!$disbursement->total_amount)
        {
            $disbursement->total_amount = 0;
        }
        if(!$disbursement->total_count)
        {
            $disbursement->total_count = 0;
        }

        return $disbursement;
    }
}
