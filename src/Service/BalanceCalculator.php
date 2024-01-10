<?php

namespace Siak\Tontine\Service;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Planning\PoolService;

class BalanceCalculator
{
    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     * @param ProfitService $profitService
     * @param SessionService $sessionService
     */
    public function __construct(protected TenantService $tenantService,
        protected PoolService $poolService, protected ProfitService $profitService,
        protected SessionService $sessionService)
    {}

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
                $query->join('pools', 'subscriptions.pool_id', '=', 'pools.id')
                    ->join('v_pool_counters', 'v_pool_counters.id', '=', 'pools.id');
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

        return !$pool->deposit_fixed ? $query->sum('deposits.amount') :
            $pool->amount * $query->count();
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getPayableAmount(Pool $pool, Session $session): int
    {
        if(!$pool->deposit_fixed)
        {
            // Sum the amounts for all deposits
            return $this->getPoolDepositAmount($pool, $session);
        }
        return $pool->amount * $this->poolService->getEnabledSessionCount($pool);
    }

    /**
     * @return string
     */
    private function getRemitmentAmountSqlValue(): string
    {
        return 'v_pool_counters.amount * (v_pool_counters.sessions - v_pool_counters.disabled_sessions)';
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
     * @param Collection $sessionIds
     * @param bool $lendable
     *
     * @return int
     */
    private function getDepositsAmount(Collection $sessionIds, bool $lendable)
    {
        return $this->getDepositQuery(true)
            ->whereIn('deposits.session_id', $sessionIds)
            ->when($lendable, function(Builder $query) {
                $query->where('pools.properties->deposit->lendable', true);
            })
            ->sum(DB::raw('deposits.amount + pools.amount'));
    }

    /**
     * @param Collection $sessionIds
     * @param bool $lendable
     *
     * @return int
     */
    private function getRemitmentsAmount(Collection $sessionIds, bool $lendable)
    {
        return
            // Remitment sum for pools with fixed deposits.
            // Each value is the pool amount multiply by the number od sessions.
            $this->getRemitmentQuery(true)
                ->whereIn('payables.session_id', $sessionIds)
                ->where('pools.properties->deposit->fixed', true)
                ->when($lendable, function(Builder $query) {
                    $query->where('pools.properties->deposit->lendable', true);
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
                    $query->where('pools.properties->deposit->lendable', true);
                })
                ->sum('deposits.amount');
    }

    /**
     * @param Collection $sessionIds
     *
     * @return int
     */
    private function getAuctionsAmount(Collection $sessionIds)
    {
        return DB::table('auctions')
            ->whereIn('session_id', $sessionIds)
            ->where('paid', true)
            ->sum('amount');
    }

    /**
     * @param Collection $sessionIds
     * @param bool $lendable
     *
     * @return int
     */
    private function getSettlementsAmount(Collection $sessionIds, bool $lendable)
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
     * @param Collection $sessionIds
     * @param bool $lendable
     *
     * @return int
     */
    private function getDisbursementsAmount(Collection $sessionIds, bool $lendable)
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
     * @param Collection $sessionIds
     * @param int $fundId
     *
     * @return int
     */
    private function getSavingsAmount(Collection $sessionIds, int $fundId)
    {
        return DB::table('savings')
            ->when($fundId === 0, fn(Builder $query) => $query->whereNull('savings.fund_id'))
            ->when($fundId !== 0, fn(Builder $query) => $query->where('savings.fund_id', $fundId))
            ->whereIn('savings.session_id', $sessionIds)
            ->sum('savings.amount');
    }

    /**
     * @param Collection $sessionIds
     * @param int $fundId
     *
     * @return int
     */
    private function getRefundsAmount(Collection $sessionIds, int $fundId)
    {
        return DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->when($fundId === 0, fn(Builder $query) => $query->whereNull('loans.fund_id'))
            ->when($fundId !== 0, fn(Builder $query) => $query->where('loans.fund_id', $fundId))
            ->sum('debts.amount');
    }

    /**
     * @param Collection $sessionIds
     * @param int $fundId
     *
     * @return int
     */
    private function getPartialRefundsAmount(Collection $sessionIds, int $fundId)
    {
        // Filter on debts that are not yet refunded.
        return DB::table('partial_refunds')
            ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('partial_refunds.session_id', $sessionIds)
            ->whereNotExists(function (Builder $query) {
                $query->select(DB::raw(1))->from('refunds')
                    ->whereColumn('refunds.debt_id', 'debts.id');
            })
            ->when($fundId === 0, fn(Builder $query) => $query->whereNull('loans.fund_id'))
            ->when($fundId !== 0, fn(Builder $query) => $query->where('loans.fund_id', $fundId))
            ->sum('partial_refunds.amount');
    }

    /**
     * @param Collection $sessionIds
     * @param int $fundId
     *
     * @return int
     */
    private function getLoansAmount(Collection $sessionIds, int $fundId)
    {
        return DB::table('debts')
            ->where('type', Debt::TYPE_PRINCIPAL)
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('loans.session_id', $sessionIds)
            ->when($fundId === 0, fn(Builder $query) => $query->whereNull('loans.fund_id'))
            ->when($fundId !== 0, fn(Builder $query) => $query->where('loans.fund_id', $fundId))
            ->sum('debts.amount');
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function getFundsAmount(Session $session)
    {
        // Each fund can have a different set of sessions, so we need to loop on all funds.
        return $this->tenantService->tontine()->funds()->active()->pluck('id')
            ->prepend(0)
            ->reduce(function($amount, $fundId) use($session) {
                $sessionIds = $this->profitService->getFundSessionIds($session, $fundId);

                return $amount
                    + $this->getSavingsAmount($sessionIds, $fundId)
                    + $this->getRefundsAmount($sessionIds, $fundId)
                    + $this->getPartialRefundsAmount($sessionIds, $fundId)
                    - $this->getLoansAmount($sessionIds, $fundId);
            }, 0);
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
        $sessionIds = $this->sessionService->getRoundSessionIds($session);

        return $this->getAuctionsAmount($sessionIds)
            + $this->getSettlementsAmount($sessionIds, true)
            + $this->getDepositsAmount($sessionIds, true)
            - $this->getRemitmentsAmount($sessionIds, true)
            - $this->getDisbursementsAmount($sessionIds, true)
            + $this->getFundsAmount($session);
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
        $sessionIds = $this->sessionService->getRoundSessionIds($session);

        return $this->getAuctionsAmount($sessionIds)
            + $this->getSettlementsAmount($sessionIds, false)
            + $this->getDepositsAmount($sessionIds, false)
            - $this->getRemitmentsAmount($sessionIds, false)
            - $this->getDisbursementsAmount($sessionIds, false)
            + $this->getFundsAmount($session);
    }

    /**
     * Get the detailed amounts.
     *
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return array<int>
     */
    public function getBalances(Session $session, bool $lendable): array
    {
        $fundAmounts = $this->tenantService->tontine()->funds()->active()->pluck('id')
            ->prepend(0)
            ->reduce(function($amounts, $fundId) use($session) {
                $sessionIds = $this->profitService->getFundSessionIds($session, $fundId);

                return [
                    'savings' => $amounts['savings'] + $this->getSavingsAmount($sessionIds, $fundId),
                    'loans' => $amounts['loans'] + $this->getLoansAmount($sessionIds, $fundId),
                    'refunds' => $amounts['refunds'] + $this->getRefundsAmount($sessionIds, $fundId) +
                        $this->getPartialRefundsAmount($sessionIds, $fundId),
                ];
            }, ['savings' => 0, 'loans' => 0, 'refunds' => 0]);

        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->sessionService->getRoundSessionIds($session);
        return [
            'auctions' => $this->getAuctionsAmount($sessionIds),
            'charges' => $this->getSettlementsAmount($sessionIds, $lendable),
            'deposits' => $this->getDepositsAmount($sessionIds, $lendable),
            'remitments' => $this->getRemitmentsAmount($sessionIds, $lendable),
            'disbursements' => $this->getDisbursementsAmount($sessionIds, $lendable),
            ...$fundAmounts,
        ];
    }
}
