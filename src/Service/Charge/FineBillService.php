<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\FineBill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class FineBillService
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
     * @return mixed
     */
    private function getQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        // The fines of the current session.
        $query = FineBill::where('charge_id', $charge->id)->where('session_id', $session->id);
        // The filter applies only to this query.
        if($onlyPaid === false)
        {
            $query = $query->whereDoesntHave('bill.settlement');
        }
        if($onlyPaid === true)
        {
            $query = $query->whereHas('bill.settlement');
        }

        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        if($prevSessions->count() === 0)
        {
            return $query;
        }

        // The fines of the previous sessions that are not yet settled.
        $unpaidQuery = FineBill::where('charge_id', $charge->id)
            ->whereIn('session_id', $prevSessions)
            ->whereDoesntHave('bill.settlement');
        // The fines of the previous sessions that are settled in the session.
        $paidQuery = FineBill::where('charge_id', $charge->id)
            ->whereIn('session_id', $prevSessions)
            ->whereHas('bill.settlement', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            });

        if($onlyPaid === false)
        {
            return $query->union($unpaidQuery);
        }
        if($onlyPaid === true)
        {
            return $query->union($paidQuery);
        }
        return $query->union($unpaidQuery)->union($paidQuery);
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
        $query = $this->getQuery($charge, $session, $onlyPaid);
        if($page > 0 )
        {
            $query->take($this->tenantService->getLimit());
            $query->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $query->with(['member', 'session', 'bill', 'bill.settlement'])->get();
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
        return $this->getQuery($charge, $session, $onlyPaid)->count();
    }
}
