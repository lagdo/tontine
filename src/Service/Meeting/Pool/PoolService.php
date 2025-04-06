<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\BalanceCalculator;
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
        protected TenantService $tenantService, protected SummaryService $summaryService,
        protected BalanceCalculator $balanceCalculator)
    {}

    /**
     * @param int $poolId    The pool id
     * @param Session $session
     *
     * @return Pool|null
     */
    public function getPool(int $poolId, Session $session): ?Pool
    {
        return Pool::ofSession($session)->with('counter')->find($poolId);
    }

    /**
     * @param Session $session
     *
     * @return Builder|Relation
     */
    private function getQuery(Session $session): Builder|Relation
    {
        return Pool::ofSession($session);
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
            ->each(function(Pool $pool) use($session) {
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
            ->with(['counter'])
            ->withCount([
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
     * @return bool
     */
    public function hasPoolWithAuction(): bool
    {
        $round = $this->tenantService->round();
        return $round && $round->pools->contains(fn($pool) => $pool->remit_auction);
    }
}
