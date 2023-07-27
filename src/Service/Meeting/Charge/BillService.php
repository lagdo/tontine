<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class BillService
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
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getSessionBillsQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        $query = Bill::whereHas('session_bill', function(Builder $query) use($charge, $session) {
            $query->where('session_id', $session->id)->where('charge_id', $charge->id);
        });
        if($onlyPaid === false)
        {
            return $query->whereDoesntHave('settlement');
        }
        if($onlyPaid === true)
        {
            return $query->whereHas('settlement');
        }
        return $query;
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $relation
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getBillsQuery(Charge $charge, Session $session, string $relation, ?bool $onlyPaid)
    {
        $query = Bill::whereHas($relation, function(Builder $query) use($charge, $session, $relation) {
            $query->where('charge_id', $charge->id);
            // Filter fine bills on current and previous sessions
            if($relation === 'fine_bill')
            {
                $sessionIds = $this->tenantService->getPreviousSessions($session);
                $query->whereIn('session_id', $sessionIds);
            }
        });
        if($onlyPaid === false)
        {
            return $query->whereDoesntHave('settlement');
        }
        if($onlyPaid === true)
        {
            return $query->whereHas('settlement', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            });
        }
        return $query->where(function(Builder $query) use($session) {
            // The bills that are paid in this session, or that are not yet paid.
            $query->orWhere(function(Builder $query) {
                $query->whereDoesntHave('settlement');
            })->orWhere(function(Builder $query) use($session) {
                $query->whereHas('settlement', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id);
                });
            });
        });
    }

    /**
     * @param Charge $charge
     *
     * @return string
     */
    private function getBillRelation(Charge $charge): string
    {
        // The intermediate relation to reach the member model.
        return $charge->is_variable ? 'fine_bill' :
            ($charge->period_session ? 'session_bill' :
            ($charge->period_round ? 'round_bill' : 'tontine_bill'));
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     * @param int $page
     *
     * @return Collection
     */
    public function getBills(Charge $charge, Session $session, ?bool $onlyPaid = null, int $page = 0): Collection
    {
        $billRelation = $this->getBillRelation($charge);
        $query = $billRelation === 'session_bill' ?
            $this->getSessionBillsQuery($charge, $session, $onlyPaid) :
            $this->getBillsQuery($charge, $session, $billRelation, $onlyPaid);
        if($page > 0 )
        {
            $query->take($this->tenantService->getLimit());
            $query->skip($this->tenantService->getLimit() * ($page - 1));
        }

        return $query->with([$billRelation . '.member', 'settlement'])
            ->orderBy('id', 'asc')
            ->get()
            ->each(function($bill) use($billRelation) {
                $bill->member = $bill->$billRelation->member;
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return int
     */
    public function getBillCount(Charge $charge, Session $session, ?bool $onlyPaid = null): int
    {
        $billRelation = $this->getBillRelation($charge);

        return $billRelation === 'session_bill' ?
            $this->getSessionBillsQuery($charge, $session, $onlyPaid)->count() :
            $this->getBillsQuery($charge, $session, $billRelation, $onlyPaid)->count();
    }
}
