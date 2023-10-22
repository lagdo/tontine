<?php

namespace Siak\Tontine\Service\Meeting\Charge;

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
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->getSession($sessionId);
    }

    /**
     * Get a single charge.
     *
     * @param int $chargeId    The charge id
     *
     * @return Charge|null
     */
    public function getCharge(int $chargeId): ?Charge
    {
        return $this->tenantService->tontine()->charges()->find($chargeId);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return mixed
     */
    private function getQuery(Charge $charge, Session $session)
    {
        // The select('bills.*') is important here, otherwise Eloquent overrides the
        // Bill model id fields with those of another model, then making the dataset incorrect.
        $query = Bill::select('bills.*');
        if($charge->is_variable)
        {
            return $query->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
                ->where('libre_bills.charge_id', $charge->id)
                ->where('libre_bills.session_id', $session->id);
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
                ->where('round_bills.charge_id', $charge->id);
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
        if(($bill->settlement->online))
        {
            throw new MessageException(trans('tontine.bill.errors.online'));
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
            ->whereHas('settlement')
            ->get()
            ->filter(function($bill) {
                return !$bill->settlement->online;
            });
        if($bills->count() === 0)
        {
            return;
        }
        DB::transaction(function() use($bills, $session) {
            foreach($bills as $bill)
            {
                $bill->settlement()->where('session_id', $session->id)->delete();
            }
        });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return object
     */
    public function getSettlement(Charge $charge, Session $session): object
    {
        $billTable = $charge->is_variable ? 'libre_bills' :
            ($charge->period_session ? 'session_bills' :
            ($charge->period_round ? 'round_bills' : 'tontine_bills'));

        return DB::table('settlements')
            ->select(DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join($billTable, "$billTable.bill_id", '=', 'bills.id')
            ->where('settlements.session_id', $session->id)
            ->where("$billTable.charge_id", $charge->id)
            ->first();
    }
}
