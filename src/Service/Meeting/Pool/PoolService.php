<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Deposit;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Meeting\Session\SummaryService;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class PoolService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SummaryService $summaryService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService,
        protected SummaryService $summaryService,
        protected BalanceCalculator $balanceCalculator)
    {}

    /**
     * @param Session $session
     * @param int $poolId    The pool id
     *
     * @return Pool|null
     */
    public function getPool(Session $session, int $poolId): ?Pool
    {
        return $session->pools()
            ->withCount(['sessions', 'disabled_sessions'])
            ->find($poolId);
    }

    /**
     * Get a list of pools with receivables.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getPoolsWithReceivables(Session $session): Collection
    {
        return $session->pools()
            ->addSelect([
                'amount_recv' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->whereHas('receivable', fn(Builder $qr) =>
                        $qr->where('session_id', $session->id)),
            ])
            ->withCount([
                'subscriptions as recv_count',
                'subscriptions as recv_paid' => function(Builder $query) use($session) {
                    $query->whereHas('receivables', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id)
                            ->whereHas('deposit', fn(Builder $qd) =>
                                $qd->where('session_id', $session->id));
                    });
                },
                'subscriptions as recv_late' => function(Builder $query) use($session) {
                    $query->whereHas('receivables', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id)
                            ->whereHas('deposit', fn(Builder $qd) =>
                                $qd->where('session_id', '!=', $session->id));
                    });
                },
            ])
            ->get();
    }

    /**
     * Get a list of pools with payables.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getPoolsWithPayables(Session $session): Collection
    {
        return $session->pools()
            ->withCount([
                'sessions',
                'disabled_sessions',
                'subscriptions as pay_paid' => function(Builder $query) use($session) {
                    $query->whereHas('payable', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('remitment');
                    });
                },
            ])
            ->get()
            ->each(function(Pool $pool) use($session) {
                // Expected
                $pool->pay_count = $this->summaryService->getSessionRemitmentCount($pool, $session);
                // Amount paid
                $pool->amount_paid = $this->balanceCalculator->getPoolRemitmentAmount($pool, $session);
            });
    }

    /**
     * Check if the current round has at least one pool with auctions.
     *
     * @param Session $session
     *
     * @return bool
     */
    public function hasPoolWithAuction(Session $session): bool
    {
        return $session->pools->contains(fn($pool) => $pool->remit_auction);
    }

    /**
     * @param Session $session
     * @param int $poolId    The pool id
     *
     * @return Pool|null
     */
    public function getRoundPool(Session $session, int $poolId): ?Pool
    {
        return $session->round->pools()
            ->withCount(['sessions', 'disabled_sessions'])
            ->find($poolId);
    }

    /**
     * Get a list of pools with late deposits.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getPoolsWithLateDeposits(Session $session): Collection
    {
        // All the round pools are returned here.
        return $session->round->pools()
            ->addSelect([
                'pools.*',
                'amount_recv' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->where('session_id', $session->id)
                    ->whereHas('receivable', fn(Builder $qr) =>
                        $qr->where('session_id', '!=', $session->id)),
            ])
            ->withCount([
                'receivables as late_count' => fn(Builder $query) =>
                    $query->late($session),
                'receivables as late_paid' => fn(Builder $query) =>
                    $query->late($session)->paid(),
            ])
            ->get();
    }
}
