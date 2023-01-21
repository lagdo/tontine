<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\FineBill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class FineService
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
     * Get a paginated list of fines.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFines(Session $session, int $page = 0): Collection
    {
        $fines = $this->tenantService->tontine()->charges()->fine()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $fines->take($this->tenantService->getLimit());
            $fines->skip($this->tenantService->getLimit() * ($page - 1));
        }
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');

        return $fines->withCount([
            'fine_bills' => function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            },
            'fine_bills as all_fine_bills_count' => function(Builder $query) use($sessionIds) {
                $query->whereIn('session_id', $sessionIds);
            },
            'fine_bills as paid_fine_bills_count' => function(Builder $query) use($session) {
                $query->where('session_id', $session->id)
                    ->whereExists(function($whereQuery) {
                        $whereQuery->select(DB::raw(1))
                            ->from('settlements')
                            ->whereColumn('settlements.bill_id', 'fine_bills.bill_id');
                    });
            },
            'fine_bills as all_paid_fine_bills_count' => function(Builder $query) use($sessionIds) {
                $query->whereIn('session_id', $sessionIds)
                    ->whereExists(function($whereQuery) {
                        $whereQuery->select(DB::raw(1))
                            ->from('settlements')
                            ->whereColumn('settlements.bill_id', 'fine_bills.bill_id');
                    });
            },
        ])->get();
    }

    /**
     * Get the number of fines.
     *
     * @return int
     */
    public function getFineCount(): int
    {
        return $this->tenantService->tontine()->charges()->fine()->count();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     *
     * @return mixed
     */
    private function getMembersQuery(Charge $charge, Session $session, ?bool $onlyFined)
    {
        $query = $this->tenantService->tontine()->members();
        if($onlyFined === false)
        {
            $query->whereDoesntHave('fine_bills', function($query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            });
        }
        elseif($onlyFined === true)
        {
            $query->whereHas('fine_bills', function($query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            });
        }
        return $query;
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Charge $charge, Session $session, ?bool $onlyFined = null, int $page = 0): Collection
    {
        $members = $this->getMembersQuery($charge, $session, $onlyFined);
        if($page > 0 )
        {
            $members->take($this->tenantService->getLimit());
            $members->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $members
            ->withCount(['fine_bills' => function(Builder $query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            }])->get();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     *
     * @return int
     */
    public function getMemberCount(Charge $charge, Session $session, ?bool $onlyFined = null): int
    {
        return $this->getMembersQuery($charge, $session, $onlyFined)->count();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return void
     */
    public function createFine(Charge $charge, Session $session, int $memberId): void
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        if(!$member)
        {
            return;
        }

        DB::transaction(function() use($charge, $session, $member) {
            $bill = Bill::create([
                'charge' => $charge->name,
                'amount' => $charge->amount,
                'issued_at' => now(),
            ]);
            $fine = new FineBill();
            $fine->charge()->associate($charge);
            $fine->member()->associate($member);
            $fine->session()->associate($session);
            $fine->bill()->associate($bill);
            $fine->save();
        });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return void
     */
    public function deleteFine(Charge $charge, Session $session, int $memberId): void
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        if(!$member)
        {
            return;
        }
        $fine = FineBill::where('charge_id', $charge->id)
            ->where('session_id', $session->id)
            ->where('member_id', $member->id)
            ->first();
        if(!$fine)
        {
            return;
        }

        DB::transaction(function() use($fine) {
            $billId = $fine->bill_id;
            $fine->delete();
            Bill::where('id', $billId)->delete();
        });
    }
}
