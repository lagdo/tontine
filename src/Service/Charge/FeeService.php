<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Tontine\TenantService;

class FeeService
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
     * Get a paginated list of fees.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFees(Session $session, int $page = 0): Collection
    {
        $fees = $this->tenantService->tontine()->charges()->fee()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $fees->take($this->tenantService->getLimit());
            $fees->skip($this->tenantService->getLimit() * ($page - 1));
        }
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');

        return $fees->withCount([
            'tontine_bills',
            'tontine_bills as paid_tontine_bills_count' => function(Builder $query) {
                $query->whereExists(function($whereQuery) {
                    $whereQuery->select(DB::raw(1))
                        ->from('settlements')
                        ->whereColumn('settlements.bill_id', 'tontine_bills.bill_id');
                });
            },
            'round_bills',
            'round_bills as paid_round_bills_count' => function(Builder $query) {
                $query->whereExists(function($whereQuery) {
                    $whereQuery->select(DB::raw(1))
                        ->from('settlements')
                        ->whereColumn('settlements.bill_id', 'round_bills.bill_id');
                });
            },
            'session_bills' => function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            },
            'session_bills as all_session_bills_count' => function(Builder $query) use($sessionIds) {
                $query->whereIn('session_id', $sessionIds);
            },
            'session_bills as paid_session_bills_count' => function(Builder $query) use($session) {
                $query->where('session_id', $session->id)
                    ->whereExists(function($whereQuery) {
                        $whereQuery->select(DB::raw(1))
                            ->from('settlements')
                            ->whereColumn('settlements.bill_id', 'session_bills.bill_id');
                    });
            },
            'session_bills as all_paid_session_bills_count' => function(Builder $query) use($sessionIds) {
                $query->whereIn('session_id', $sessionIds)
                    ->whereExists(function($whereQuery) {
                        $whereQuery->select(DB::raw(1))
                            ->from('settlements')
                            ->whereColumn('settlements.bill_id', 'session_bills.bill_id');
                    });
            },
        ])->get();
    }

    /**
     * Get the number of fees.
     *
     * @return int
     */
    public function getFeeCount(): int
    {
        return $this->tenantService->tontine()->charges()->fee()->count();
    }
}
