<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Support\Collection;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\FineBill;
use Siak\Tontine\Model\SessionBill;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\TontineBill;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Tontine\TenantService;

class SettlementService
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
        if($charge->is_fine)
        {
            return FineBill::where('session_id', $session->id);
        }
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
            $query->whereDoesntHave('bill.settlement');
        }
        elseif($onlyPaid === true)
        {
            $query->whereHas('bill.settlement');
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

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return mixed
     */
    private function getBillQuery(Charge $charge, Session $session)
    {
        // The select('bills.*') is important here, otherwise Eloquent overrides the
        // Bill model id fields with those of another model, then making the dataset incorrect.
        $query = Bill::select('bills.*')->with('settlement');
        if($charge->is_fine)
        {
            return $query->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
                ->where('fine_bills.session_id', $session->id);
        }
        if($charge->period_session)
        {
            return $query->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
                ->where('session_bills.session_id', $session->id);
        }
        if($charge->period_round)
        {
            return $query->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
                ->where('round_bills.round_id', $session->round_id);
        }
        // if($charge->period_once)
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        return $query->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereIn('tontine_bills.member_id', $memberIds);
    }

    /**
     * Create a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    public function createSettlement(Charge $charge, Session $session, int $billId): void
    {
        $bill = $this->getBillQuery($charge, $session)->find($billId);
        // Return if the bill is not found or the bill is already settled.
        if(!$bill || ($bill->settlement))
        {
            return;
        }
        $settlement = new Settlement();
        $settlement->bill()->associate($bill);
        $settlement->session()->associate($session);
        $settlement->save();
    }

    /**
     * Delete a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    public function deleteSettlement(Charge $charge, Session $session, int $billId): void
    {
        $bill = $this->getBillQuery($charge, $session)->find($billId);
        // Return if the bill is not found or the bill is not settled.
        if(!$bill || !($bill->settlement))
        {
            return;
        }
        $bill->settlement()->delete();
    }
}
