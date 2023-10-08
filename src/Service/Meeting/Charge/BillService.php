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
        return Bill::when($onlyPaid === false, function($query){
                return $query->whereDoesntHave('settlement');
            })
            ->when($onlyPaid === true, function($query) {
                return $query->whereHas('settlement');
            })
            ->whereHas('session_bill', function(Builder $query) use($charge, $session) {
                $query->where('session_id', $session->id)->where('charge_id', $charge->id);
            });
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
        return Bill::whereHas($relation, function(Builder $query) use($charge, $session, $relation) {
                $query->where('charge_id', $charge->id);
                // Filter fine bills on current and previous sessions
                if($relation === 'fine_bill')
                {
                    $sessionIds = $this->tenantService->getSessionIds($session);
                    $query->whereIn('session_id', $sessionIds);
                }
            })
            ->when($onlyPaid === false, function($query) {
                return $query->whereDoesntHave('settlement');
            })
            ->when($onlyPaid === true, function($query) use($session) {
                return $query->whereHas('settlement', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id);
                });
            })
            ->when($onlyPaid === null, function($query) use($session) {
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
        $billRelations = !$charge->is_variable ? [$billRelation . '.member', 'settlement'] :
            ['fine_bill.session', 'fine_bill.member', 'settlement'];

        return $query->with($billRelations)
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('id', 'asc')
            ->get()
            ->each(function($bill) use($billRelation, $charge) {
                $bill->member = $bill->$billRelation->member;
                $bill->session = $charge->is_variable ? $bill->fine_bill->session : null;
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
