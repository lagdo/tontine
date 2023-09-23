<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class PoolService
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
     * @var SummaryService
     */
    protected SummaryService $summaryService;

    /**
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SummaryService $summaryService
     * @param SessionService $sessionService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        SummaryService $summaryService, SessionService $sessionService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->summaryService = $summaryService;
        $this->sessionService = $sessionService;
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
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * @param int $page
     *
     * @return Builder
     */
    public function getPoolsQuery(int $page = 0)
    {
        // Take only pools with at least one subscription.
        return $this->tenantService->round()->pools()
            ->whereHas('subscriptions')
            ->page($page, $this->tenantService->getLimit());
    }

    /**
     * Get a paginated list of pools with receivables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolsWithReceivables(Session $session, int $page = 0): Collection
    {
        return $this->getPoolsQuery($page)->withCount([
            'subscriptions as recv_count',
            'subscriptions as recv_paid' => function(Builder $query) use($session) {
                $query->whereHas('receivables', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id)->whereHas('deposit');
                });
            },
        ])->get();
    }

    /**
     * Get a paginated list of pools with payables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolsWithPayables(Session $session, int $page = 0): Collection
    {
        return $this->getPoolsQuery($page)->withCount([
            'subscriptions as pay_paid' => function(Builder $query) use($session) {
                $query->whereHas('payable', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id)->whereHas('remitment');
                });
            },
        ])->get()->each(function($pool) use($session) {
            // Expected
            $pool->pay_count = $this->summaryService->getSessionRemitmentCount($pool, $session);
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
}
