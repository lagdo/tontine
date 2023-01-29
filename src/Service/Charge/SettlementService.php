<?php

namespace Siak\Tontine\Service\Charge;

use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
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
     *
     * @return mixed
     */
    private function getQuery(Charge $charge)
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
        $bill = $this->getQuery($charge)->find($billId);
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
        $bill = $this->getQuery($charge)->find($billId);
        // Return if the bill is not found or the bill is not settled.
        if(!$bill || !($bill->settlement) || $bill->settlement->session_id !== $session->id)
        {
            return;
        }
        $bill->settlement()->delete();
    }
}
