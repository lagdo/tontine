<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\FineBill;
use Siak\Tontine\Model\SessionBill;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\TontineBill;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

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
     * @param Builder $query
     * @param boolean|null $onlyPaid
     *
     * @return Builder
     */
    private function filterQuery(Builder $query, ?bool $onlyPaid): Builder
    {
        if($onlyPaid === false)
        {
            return $query->whereDoesntHave('bill.settlement');
        }
        if($onlyPaid === true)
        {
            return $query->whereHas('bill.settlement');
        }
        return $query;
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return mixed
     */
    private function getFinesQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        // The fines of the current session.
        $query = FineBill::select('fine_bills.*')
            ->where('fine_bills.charge_id', $charge->id)
            ->where('fine_bills.session_id', $session->id);
        // The filter applies only to this query.
        $query = $this->filterQuery($query, $onlyPaid);

        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        if($prevSessions->count() === 0)
        {
            return $query;
        }

        // The fines of the previous sessions that are not yet settled.
        $unpaidQuery = FineBill::select('fine_bills.*')
            ->where('fine_bills.charge_id', $charge->id)
            ->whereIn('fine_bills.session_id', $prevSessions)
            ->whereNotExists(function($whereQuery) {
                $whereQuery->select(DB::raw(1))
                    ->from('settlements')
                    ->whereColumn('settlements.bill_id', 'fine_bills.bill_id');
            });
        // The fines of the previous sessions that are settled in the session.
        $paidQuery = FineBill::select('fine_bills.*')
            ->join('settlements', 'fine_bills.bill_id', '=', 'settlements.bill_id')
            ->where('fine_bills.charge_id', $charge->id)
            ->whereIn('fine_bills.session_id', $prevSessions)
            ->where('settlements.session_id', $session->id);

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
     *
     * @return mixed
     */
    private function getFeesQuery(Charge $charge, Session $session)
    {
        if($charge->period_session)
        {
            return SessionBill::where('charge_id', $charge->id)->where('session_id', $session->id);
        }
        if($charge->period_round)
        {
            return RoundBill::where('charge_id', $charge->id)->where('round_id', $session->round_id);
        }
        // if($charge->period_once)
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        return TontineBill::where('charge_id', $charge->id)->whereIn('member_id', $memberIds);
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
        if($charge->is_fine)
        {
            return $this->getFinesQuery($charge, $session, $onlyPaid);
        }
        return $this->filterQuery($this->getFeesQuery($charge, $session), $onlyPaid);
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
     *
     * @return mixed
     */
    private function getBillQuery(Charge $charge)
    {
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        // The select('bills.*') is important here, otherwise Eloquent overrides the
        // Bill model id fields with those of another model, then making the dataset incorrect.
        $query = Bill::select('bills.*')->with('settlement');
        if($charge->is_fine)
        {
            return $query->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
                ->whereIn('fine_bills.member_id', $memberIds);
        }
        if($charge->period_session)
        {
            return $query->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
                ->whereIn('session_bills.member_id', $memberIds);
        }
        if($charge->period_round)
        {
            return $query->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
                ->whereIn('round_bills.member_id', $memberIds);
        }
        // if($charge->period_once)
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
        $bill = $this->getBillQuery($charge)->find($billId);
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
        $bill = $this->getBillQuery($charge)->find($billId);
        // Return if the bill is not found or the bill is not settled.
        if(!$bill || !($bill->settlement) || $bill->settlement->session_id !== $session->id)
        {
            return;
        }
        $bill->settlement()->delete();
    }
}
