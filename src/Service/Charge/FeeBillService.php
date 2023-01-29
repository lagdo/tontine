<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\SessionBill;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\TontineBill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class FeeBillService
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
     *
     * @return mixed
     */
    private function getBillsQuery(Charge $charge, Session $session)
    {
        if($charge->period_session)
        {
            return SessionBill::where('session_id', $session->id);
        }
        if($charge->period_round)
        {
            return RoundBill::where('round_id', $session->round_id);
        }
        // if($charge->period_once)
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        return TontineBill::whereIn('member_id', $memberIds);
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
        $query = $this->getBillsQuery($charge, $session);
        if($onlyPaid === false)
        {
            return $query->whereDoesntHave('bill.settlement');
        }
        if($onlyPaid === true)
        {
            return $query->whereHas('bill.settlement');
        }
        return $query->where('charge_id', $charge->id);
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
        return $query->with(['member', 'bill', 'bill.settlement'])->get();
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
