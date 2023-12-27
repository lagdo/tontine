<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\PoolTrait;

class PoolService
{
    use PoolTrait;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param BalanceCalculator $balanceCalculator
     * @param SummaryService $summaryService
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected BalanceCalculator $balanceCalculator,
        protected SummaryService $summaryService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->balanceCalculator = $balanceCalculator;
        $this->summaryService = $summaryService;
    }

    /**
     * @return Tontine|null
     */
    public function getTontine(): ?Tontine
    {
        return $this->tenantService->tontine();
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->getSession($sessionId);
    }

    /**
     * @param Session $session
     *
     * @return Builder
     */
    private function getQuery(Session $session)
    {
        $poolClosure = function(Builder $query) use($session) {
            $query->whereHas('subscriptions', function(Builder $query) use($session) {
                $query->whereHas('receivables', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id);
                });
            });
        };
        $round = $this->tenantService->round();
        $date = $session->start_at;
        return $this->getPoolsQuery($round, $date, $date, $poolClosure);
    }

    /**
     * Get a paginated list of pools with receivables.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getPoolsWithReceivables(Session $session): Collection
    {
        return $this->getQuery($session)
            ->withCount([
                'subscriptions as recv_count',
                'subscriptions as recv_paid' => function(Builder $query) use($session) {
                    $query->whereHas('receivables', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('deposit');
                    });
                },
            ])
            ->get()
            ->each(function($pool) use($session) {
                // Amount paid
                $pool->amount_recv = $this->balanceCalculator->getPoolDepositAmount($pool, $session);
            });
    }

    /**
     * Get a paginated list of pools with payables.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getPoolsWithPayables(Session $session): Collection
    {
        return $this->getQuery($session)
            ->withCount([
                'subscriptions as pay_paid' => function(Builder $query) use($session) {
                    $query->whereHas('payable', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('remitment');
                    });
                },
            ])
            ->get()
            ->each(function($pool) use($session) {
                // Expected
                $pool->pay_count = $this->summaryService->getSessionRemitmentCount($pool, $session);
                // Amount paid
                $pool->amount_paid = $this->balanceCalculator->getPoolRemitmentAmount($pool, $session);
            });
    }

    /**
     * Get a single pool.
     *
     * @param int $poolId    The pool id
     *
     * @return Pool|null
     */
    public function getPool(int $poolId): ?Pool
    {
        return $this->tenantService->getPool($poolId);
    }

    /**
     * Check if the current tontine has at least one pool with auctions.
     *
     * @return bool
     */
    public function hasPoolWithAuction(): bool
    {
        $round = $this->tenantService->round();
        return $round && $round->pools->contains(function($pool) {
            return $pool->remit_auction;
        });
    }
}
