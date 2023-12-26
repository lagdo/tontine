<?php

namespace Siak\Tontine\Service;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Model\Pool;

class BalanceCalculator
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * @param Receivable $receivable
     *
     * @return int
     */
    public function getReceivableAmount(Receivable $receivable): int
    {
        if($receivable->subscription->pool->deposit_fixed)
        {
            return $receivable->subscription->pool->amount;
        }

        return !$receivable->deposit ? 0 : $receivable->deposit->amount;
    }

    /**
     * @param bool $withPoolTable
     *
     * @return Builder
     */
    private function getDepositQuery(bool $withPoolTable): Builder
    {
        return DB::table('deposits')
            ->join('receivables', 'deposits.receivable_id', '=', 'receivables.id')
            ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
            ->when($withPoolTable, function(Builder $query) {
                $query->join('pools', 'subscriptions.pool_id', '=', 'pools.id');
            });
    }

    /**
     * @param bool $withPoolTable
     *
     * @return Builder
     */
    private function getRemitmentQuery(bool $withPoolTable): Builder
    {
        return DB::table('remitments')
            ->join('payables', 'remitments.payable_id', '=', 'payables.id')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->when($withPoolTable, function(Builder $query) {
                $query->join('pools', 'subscriptions.pool_id', '=', 'pools.id');
            });
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getPoolDepositAmount(Pool $pool, Session $session): int
    {
        $query = $this->getDepositQuery(false)
            ->where('receivables.session_id', $session->id)
            ->where('subscriptions.pool_id', $pool->id);

        return $pool->deposit_fixed ?
            $pool->amount * $query->count() : $query->sum('deposits.amount');
    }

    /**
     * @param Payable $payable
     * @param Session $session
     *
     * @return int
     */
    public function getPayableAmount(Payable $payable, Session $session): int
    {
        $pool = $payable->subscription->pool;
        if($pool->deposit_fixed)
        {
            return $pool->amount * $this->tenantService->countEnabledSessions($pool);
        }
        // Sum the amounts for all deposits
        return $this->getPoolDepositAmount($pool, $session);
    }

    /**
     * @return string
     */
    private function getRemitmentAmountSqlValue(): string
    {
        return 'pools.amount * (' . $this->tenantService->getSessions()->count() .
            ' - (select count(*) from pool_session_disabled where pool_id = pools.id))';
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getPoolRemitmentAmount(Pool $pool, Session $session): int
    {
        if(!$pool->deposit_fixed)
        {
            // Sum the amounts for all deposits
            return $this->getPoolDepositAmount($pool, $session);
        }

        return $this->getRemitmentQuery(true)
            ->where('payables.session_id', $session->id)
            ->where('subscriptions.pool_id', $pool->id)
            ->sum(DB::raw($this->getRemitmentAmountSqlValue()));
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function depositAmount(Collection $sessionIds, bool $lendable)
    {
        return $this->getDepositQuery(true)
            ->whereIn('deposits.session_id', $sessionIds)
            ->when($lendable, function(Builder $query) {
                $query->where('pools.properties->remit->lendable', true);
            })
            ->sum(DB::raw('deposits.amount + pools.amount'));
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function remitmentAmount(Collection $sessionIds, bool $lendable)
    {
        return
            // Remitment sum for pools with fixed deposits.
            // Each value is the pool amount multiply by the number od sessions.
            $this->getRemitmentQuery(true)
                ->whereIn('payables.session_id', $sessionIds)
                ->where('pools.properties->deposit->fixed', true)
                ->when($lendable, function(Builder $query) {
                    $query->where('pools.properties->remit->lendable', true);
                })
                ->sum(DB::raw($this->getRemitmentAmountSqlValue()))
            // Remitment sum for pools with libre deposits.
            // Each value is the sum of deposits for the given pool.
            + $this->getDepositQuery(true)
                ->whereIn('deposits.session_id', $sessionIds)
                ->whereExists(function(Builder $query) {
                    $query->select(DB::raw(1))->from('remitments')
                        ->join(DB::raw('payables p'), 'remitments.payable_id', '=', 'p.id')
                        ->join(DB::raw('subscriptions s'), 'p.subscription_id', '=', 's.id')
                        ->whereColumn('p.session_id', 'deposits.session_id')
                        ->whereColumn('s.pool_id', 'pools.id');
                })
                ->where('pools.properties->deposit->fixed', false)
                ->when($lendable, function(Builder $query) {
                    $query->where('pools.properties->remit->lendable', true);
                })
                ->sum('deposits.amount');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function auctionAmount(Collection $sessionIds)
    {
        return DB::table('auctions')
            ->whereIn('session_id', $sessionIds)
            ->where('paid', true)
            ->sum('amount');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function savingAmount(Collection $sessionIds)
    {
        return DB::table('savings')
            ->whereIn('session_id', $sessionIds)
            ->sum('amount');
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function settlementAmount(Collection $sessionIds, bool $lendable)
    {
        return DB::table('settlements')
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->when($lendable, function(Builder $query) {
                $query->where('bills.lendable', true);
            })
            ->sum('bills.amount');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function refundAmount(Collection $sessionIds)
    {
        return DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->sum('debts.amount');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function partialRefundAmount(Collection $sessionIds)
    {
        // Filter on debts that are not yet refunded.
        return DB::table('partial_refunds')
            ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
            ->whereIn('partial_refunds.session_id', $sessionIds)
            ->whereNotExists(function (Builder $query) {
                $query->select(DB::raw(1))->from('refunds')
                    ->whereColumn('refunds.debt_id', 'debts.id');
            })
            ->sum('partial_refunds.amount');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function debtAmount(Collection $sessionIds)
    {
        return DB::table('debts')
            ->where('type', Debt::TYPE_PRINCIPAL)
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('loans.session_id', $sessionIds)
            ->sum('amount');
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function disbursementAmount(Collection $sessionIds, bool $lendable)
    {
        return DB::table('disbursements')
            ->whereIn('session_id', $sessionIds)
            ->when($lendable, function(Builder $query) {
                $query->whereNull('charge_id')
                    ->orWhereExists(function(Builder $query) {
                        $query->select(DB::raw(1))->from('charges')
                            ->whereColumn('charges.id', 'disbursements.charge_id')
                            ->where('lendable', true);
                    });
            })
            ->sum('amount');
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getBalanceForLoan(Session $session): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->getSessionIds($session);

        return $this->auctionAmount($sessionIds) + $this->savingAmount($sessionIds) +
            $this->settlementAmount($sessionIds, true) + $this->refundAmount($sessionIds) +
            $this->partialRefundAmount($sessionIds) + $this->depositAmount($sessionIds, true) -
            $this->remitmentAmount($sessionIds, true) - $this->debtAmount($sessionIds) -
            $this->disbursementAmount($sessionIds, true);
    }

    /**
     * Get the amount available for disbursement.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getTotalBalance(Session $session): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->getSessionIds($session);

        return $this->auctionAmount($sessionIds) + $this->savingAmount($sessionIds) +
            $this->settlementAmount($sessionIds, false) + $this->refundAmount($sessionIds) +
            $this->partialRefundAmount($sessionIds) + $this->depositAmount($sessionIds, false) -
            $this->remitmentAmount($sessionIds, false) - $this->debtAmount($sessionIds) -
            $this->disbursementAmount($sessionIds, false);
    }
}
