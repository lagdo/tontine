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
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getSessionBillsQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        $query = SessionBill::where('session_id', $session->id)
            ->where('charge_id', $charge->id);
        if($onlyPaid === false)
        {
            $query = $query->whereDoesntHave('bill.settlement');
        }
        if($onlyPaid === true)
        {
            $query = $query->whereHas('bill.settlement');
        }
        return $query;
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getRoundBillsQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        $unpaidQuery = RoundBill::where('round_id', $session->round_id)
            ->where('charge_id', $charge->id)
            ->whereDoesntHave('bill.settlement');
        // Only the bills that are paid in this session.
        $paidQuery = RoundBill::where('round_id', $session->round_id)
            ->where('charge_id', $charge->id)
            ->whereHas('bill.settlement', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            });
        if($onlyPaid === false)
        {
            return $unpaidQuery;
        }
        if($onlyPaid === true)
        {
            return $paidQuery;
        }
        return $paidQuery->union($unpaidQuery);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getTontineBillsQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        $unpaidQuery = TontineBill::whereIn('member_id', $memberIds)
            ->where('charge_id', $charge->id)
            ->whereDoesntHave('bill.settlement');
        // Only the bills that are paid in this session.
        $paidQuery = TontineBill::whereIn('member_id', $memberIds)
            ->where('charge_id', $charge->id)
            ->whereHas('bill.settlement', function(Builder $query) use($session) {
                $query->where('session_id', $session->id);
            });
        if($onlyPaid === false)
        {
            return $unpaidQuery;
        }
        if($onlyPaid === true)
        {
            return $paidQuery;
        }
        return $paidQuery->union($unpaidQuery);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        if($charge->period_session)
        {
            return $this->getSessionBillsQuery($charge, $session, $onlyPaid);
        }
        if($charge->period_round)
        {
            return $this->getRoundBillsQuery($charge, $session, $onlyPaid);
        }
        // if($charge->period_once)
        return $this->getTontineBillsQuery($charge, $session, $onlyPaid);
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
        return $query->with(['member', 'bill', 'bill.settlement'])
            ->orderBy('id', 'asc')->get();
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
