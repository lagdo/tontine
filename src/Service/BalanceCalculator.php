<?php

namespace Siak\Tontine\Service;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Deposit;
use Siak\Tontine\Model\Disbursement;
use Siak\Tontine\Model\Funding;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Remitment;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
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
     * Get the number of sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return int
     */
    public function enabledSessionCount(Pool $pool): int
    {
        return $this->tenantService->round()->sessions->count() - $pool->disabledSessions->count();
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
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getPoolDepositAmount(Pool $pool, Session $session): int
    {
        $query = Deposit::whereHas('receivable', function($query) use($pool, $session) {
            $query->where('session_id', $session->id)
                ->whereHas('subscription', function($query) use($pool) {
                    $query->where('pool_id', $pool->id);
                });
        });
        return $pool->deposit_fixed ?
            $pool->amount * $query->count() : ($query->sum('amount') ?? 0);
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
        if($pool->remit_fixed)
        {
            if($pool->deposit_fixed)
            {
                return $pool->amount * $this->enabledSessionCount($pool);
            }
            // Sum the amounts for all deposits
            return Deposit::whereHas('receivable', function($query) use($pool, $session) {
                $query->where('session_id', $session->id)
                    ->whereHas('subscription', function($query) use($pool) {
                        $query->where('pool_id', $pool->id);
                    });
            })->sum('amount') ?? 0;
        }
        return $payable->remitment ? $payable->remitment->amount : 0;
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getPoolRemitmentAmount(Pool $pool, Session $session): int
    {
        if(!$pool->remit_fixed)
        {
            // Sum the amounts for all remitments
            return Remitment::whereHas('payable', function($query) use($pool, $session) {
                $query->where('session_id', $session->id)
                    ->whereHas('subscription', function($query) use($pool) {
                        $query->where('pool_id', $pool->id);
                    });
            })->sum('amount') ?? 0;
        }
        if(!$pool->deposit_fixed)
        {
            // Sum the amounts for all deposits
            return Deposit::whereHas('receivable', function($query) use($pool, $session) {
                $query->where('session_id', $session->id)
                    ->whereHas('subscription', function($query) use($pool) {
                        $query->where('pool_id', $pool->id);
                    });
            })->sum('amount') ?? 0;
        }

        // Multiply the number of remitments by the number of sessions by the pool amount
        return Remitment::whereHas('payable', function($query) use($pool, $session) {
            $query->where('session_id', $session->id)
                ->whereHas('subscription', function($query) use($pool) {
                    $query->where('pool_id', $pool->id);
                });
        })->count() * $pool->amount * $this->enabledSessionCount($pool);
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function depositAmount(Collection $sessionIds, bool $lendable)
    {
        return Deposit::select(DB::raw('sum(deposits.amount + pools.amount) as total'))
            ->join('receivables', 'deposits.receivable_id', '=', 'receivables.id')
            ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
            ->join('pools', 'subscriptions.pool_id', '=', 'pools.id')
            ->whereIn('deposits.session_id', $sessionIds)
            ->when($lendable, function($query) {
                $query->where('pools.properties->remit->lendable', true);
            })
            ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function remitmentAmount(Collection $sessionIds, bool $lendable)
    {
        $sessionCount = $this->tenantService->round()->sessions->count();
        $sqlSessionCount = "$sessionCount - " .
            "(select count(*) from pool_session_disabled where pool_id = pools.id)";
        return
            // Remitment sum for pools with fixed deposits and fixed remitments.
            // Each value is the pool amount multiply by the number od sessions.
            Remitment::select(DB::raw("sum(pools.amount * ($sqlSessionCount)) as total"))
                ->join('payables', 'remitments.payable_id', '=', 'payables.id')
                ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
                ->join('pools', 'subscriptions.pool_id', '=', 'pools.id')
                ->whereIn('payables.session_id', $sessionIds)
                ->where('pools.properties->deposit->fixed', true)
                ->where('pools.properties->remit->fixed', true)
                ->when($lendable, function($query) {
                    $query->where('pools.properties->remit->lendable', true);
                })
                ->value('total') ?? 0
            // Remitment sum for pools with libre deposits and fixed remitments.
            // Each value is the sum of deposits for the given pool.
            + Deposit::select(DB::raw('sum(deposits.amount) as total'))
                ->join('receivables', 'deposits.receivable_id', '=', 'receivables.id')
                ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
                ->join('pools', 'subscriptions.pool_id', '=', 'pools.id')
                ->whereIn('deposits.session_id', $sessionIds)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))->from('remitments')
                        ->join(DB::raw('payables p'), 'remitments.payable_id', '=', 'p.id')
                        ->join(DB::raw('subscriptions s'), 'p.subscription_id', '=', 's.id')
                        ->whereColumn('p.session_id', 'deposits.session_id')
                        ->whereColumn('s.pool_id', 'pools.id');
                })
                ->where('pools.properties->deposit->fixed', false)
                ->where('pools.properties->remit->fixed', true)
                ->when($lendable, function($query) {
                    $query->where('pools.properties->remit->lendable', true);
                })
                ->value('total') ?? 0
            // Remitment sum for pools with libre remitments.
            + Remitment::select(DB::raw('sum(remitments.amount) as total'))
                ->join('payables', 'remitments.payable_id', '=', 'payables.id')
                ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
                ->join('pools', 'subscriptions.pool_id', '=', 'pools.id')
                ->whereIn('payables.session_id', $sessionIds)
                ->where('pools.properties->remit->fixed', false)
                ->when($lendable, function($query) {
                    $query->where('pools.properties->remit->lendable', true);
                })
                ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function auctionAmount(Collection $sessionIds)
    {
        return Auction::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->where('paid', true)
            ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function fundingAmount(Collection $sessionIds)
    {
        return Funding::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function settlementAmount(Collection $sessionIds, bool $lendable)
    {
        return Settlement::select(DB::raw('sum(bills.amount) as total'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->when($lendable, function($query) {
                $query->where('bills.lendable', true);
            })
            ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function refundAmount(Collection $sessionIds)
    {
        return Refund::select(DB::raw('sum(debts.amount) as total'))
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function partialRefundAmount(Collection $sessionIds)
    {
        // Filter on debts that are not yet refunded.
        return PartialRefund::select(DB::raw('sum(partial_refunds.amount) as total'))
            ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
            ->whereIn('partial_refunds.session_id', $sessionIds)
            ->whereNotExists(function (Builder $query) {
                $query->select(DB::raw(1))->from('refunds')
                    ->whereColumn('refunds.debt_id', 'debts.id');
            })
            ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     *
     * @return int
     */
    private function debtAmount(Collection $sessionIds)
    {
        return Debt::principal()->select(DB::raw('sum(debts.amount) as total'))
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('loans.session_id', $sessionIds)
            ->value('total') ?? 0;
    }

    /**
     * @param Session $session    The session
     * @param bool $lendable
     *
     * @return int
     */
    private function disbursementAmount(Collection $sessionIds, bool $lendable)
    {
        return Disbursement::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->when($lendable, function($query) {
                $query->whereNull('charge_id')
                    ->orWhereHas('charge', function($query) {
                        $query->where('lendable', true);
                    });
            })
            ->value('total') ?? 0;
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
        $sessionIds = $this->tenantService->getPreviousSessions($session);

        return $this->auctionAmount($sessionIds) + $this->fundingAmount($sessionIds) +
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
        $sessionIds = $this->tenantService->getPreviousSessions($session);

        return $this->auctionAmount($sessionIds) + $this->fundingAmount($sessionIds) +
            $this->settlementAmount($sessionIds, false) + $this->refundAmount($sessionIds) +
            $this->partialRefundAmount($sessionIds) + $this->depositAmount($sessionIds, false) -
            $this->remitmentAmount($sessionIds, false) - $this->debtAmount($sessionIds) -
            $this->disbursementAmount($sessionIds, false);
    }
}
