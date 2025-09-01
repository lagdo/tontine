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
                'recv_amount' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->whereHas('receivable', fn(Builder $qr) =>
                        $qr->whereSession($session)),
                'extra_amount' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->whereSession($session)
                    ->whereHas('receivable', fn(Builder $qr) =>
                        $qr->where('session_id', '!=', $session->id)),
            ])
            ->withCount([
                'receivables as recv_count' => fn(Builder $query) =>
                    $query->whereSession($session),
                'receivables as paid_count' => fn(Builder $query) =>
                    $query->whereSession($session)->paid(),
                'receivables as paid_here' => fn(Builder $query) =>
                    $query->whereSession($session)->paidHere($session),
                'receivables as paid_early' => fn(Builder $query) =>
                    $query->whereSession($session)->paidEarlier($session),
                'receivables as paid_late' => fn(Builder $query) =>
                    $query->whereSession($session)->paidLater($session),
                'receivables as next_early' => fn(Builder $query) =>
                    $query->succeedes($session)->paidHere($session),
                'receivables as prev_late' => fn(Builder $query) =>
                    $query->precedes($session)->paidHere($session),
            ])
            ->get()
            ->each(fn(Pool $pool) => $pool->recv_amount ??= 0);
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
                'payables as pay_paid' => fn(Builder $query) =>
                    $query->whereSession($session)->whereHas('remitment'),
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
                'amount_recv' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->whereSession($session)
                    ->whereHas('receivable', fn(Builder $rq) => $rq->precedes($session)),
            ])
            ->withCount([
                'receivables as late_count' => fn(Builder $query) =>
                    $query->late($session),
                'receivables as late_paid' => fn(Builder $query) =>
                    $query->precedes($session)->paidHere($session),
            ])
            ->get()
            ->each(fn(Pool $pool) => $pool->amount_recv ??= 0);
    }

    /**
     * Get a list of pools with early deposits.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getPoolsWithEarlyDeposits(Session $session): Collection
    {
        // All the round pools are returned here.
        return $session->round->pools()
            ->addSelect([
                'amount_recv' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->whereSession($session)
                    ->whereHas('receivable', fn(Builder $rq) => $rq->succeedes($session)),
            ])
            ->withCount([
                'receivables as early_count' => fn(Builder $query) =>
                    $query->succeedes($session)->paidHere($session),
            ])
            ->get()
            ->each(fn(Pool $pool) => $pool->amount_recv ??= 0);
    }

    /**
     * @param Session $session
     * @param  bool $strictly
     *
     * @return Collection
     */
    public function getNextSessions(Session $session, bool $strictly = true): Collection
    {
        return $session->round->sessions()
            ->opened()
            ->succeedes($session, $strictly)
            ->orderBy('day_date', 'asc')
            ->pluck('title', 'id');
    }

    /**
     * @param Session $session
     * @param int $nextSessionId
     *
     * @return Session|null
     */
    public function getNextSession(Session $session, int $nextSessionId): ?Session
    {
        return $session->round->sessions()->opened()
            ->where('day_date', '>', $session->day_date)
            ->find($nextSessionId);
    }
}
