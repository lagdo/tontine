<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\TenantService;

use function trans;

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
     * @return Builder|Relation
     */
    private function getQuery(Charge $charge, Session $session): Builder|Relation
    {
        // The select('bills.*') is important here, otherwise Eloquent overrides the
        // Bill model id fields with those of another model, then making the dataset incorrect.
        $query = Bill::select('bills.*');
        if($charge->is_variable)
        {
            return $query->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
                ->join('sessions', 'sessions.id', '=', 'libre_bills.session_id')
                ->where('libre_bills.charge_id', $charge->id)
                ->where('sessions.round_id', $session->round_id);
        }
        if($charge->period_session)
        {
            return $query->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
                ->where('session_bills.charge_id', $charge->id)
                ->where('session_bills.session_id', $session->id);
        }
        if($charge->period_round)
        {
            return $query->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
                ->where('round_bills.charge_id', $charge->id)
                ->where('round_bills.round_id', $session->round_id);
        }
        // if($charge->period_once)
        return $query->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->where('tontine_bills.charge_id', $charge->id);
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
        $bill = $this->getQuery($charge, $session)->with('settlement')->find($billId);
        // Return if the bill is not found or the bill is already settled.
        if(!$bill || ($bill->settlement))
        {
            throw new MessageException(trans('tontine.bill.errors.not_found'));
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
        $bill = $this->getQuery($charge, $session)->with('settlement')->find($billId);
        // Return if the bill is not found or the bill is not settled.
        if(!$bill || !($bill->settlement))
        {
            throw new MessageException(trans('tontine.bill.errors.not_found'));
        }
        if((!$bill->settlement->editable))
        {
            throw new MessageException(trans('tontine.errors.editable'));
        }
        $bill->settlement()->where('session_id', $session->id)->delete();
    }

    /**
     * Create a settlement for all unpaid bills
     *
     * @param Charge $charge
     * @param Session $session
     *
     * @return void
     */
    public function createAllSettlements(Charge $charge, Session $session): void
    {
        $bills = $this->getQuery($charge, $session)->whereDoesntHave('settlement')->get();
        if($bills->count() === 0)
        {
            return;
        }

        DB::transaction(function() use($bills, $session) {
            foreach($bills as $bill)
            {
                $settlement = new Settlement();
                $settlement->bill()->associate($bill);
                $settlement->session()->associate($session);
                $settlement->save();
            }
        });
    }

    /**
     * Delete all settlements
     *
     * @param Charge $charge
     * @param Session $session
     *
     * @return void
     */
    public function deleteAllSettlements(Charge $charge, Session $session): void
    {
        $bills = $this->getQuery($charge, $session)
            ->with(['settlement'])
            ->whereHas('settlement')
            ->get()
            ->filter(function($bill) {
                return $bill->settlement->editable;
            });
        if($bills->count() === 0)
        {
            return;
        }

        Settlement::whereIn('bill_id', $bills->pluck('id'))
            ->where('session_id', $session->id)
            ->delete();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return object
     */
    public function getSettlement(Charge $charge, Session $session): object
    {
        $query = DB::table('settlements')
            ->select(DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id');
        if($charge->is_variable)
        {
            return $query->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
                ->where('settlements.session_id', $session->id)
                ->where('libre_bills.charge_id', $charge->id)
                ->first();
        }
        if($charge->period_session)
        {
            // For session charges, count the bills in the current session that are settled.
            return $query->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
                ->where('session_bills.charge_id', $charge->id)
                ->where('session_bills.session_id', $session->id)
                ->first();
        }
        if($charge->period_round)
        {
            return $query->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
                ->where('settlements.session_id', $session->id)
                ->where('round_bills.charge_id', $charge->id)
                ->first();
        }
        // if($charge->period_once)
        return $query->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->where('settlements.session_id', $session->id)
            ->where('tontine_bills.charge_id', $charge->id)
            ->first();
    }
}
