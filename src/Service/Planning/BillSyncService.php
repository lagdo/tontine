<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\OnetimeBill;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\SessionBill;
use DateTime;

use function now;

class BillSyncService
{
    /**
     * @param Charge $charge
     * @param DateTime $today
     *
     * @return Bill
     */
    private function createBill(Charge $charge, DateTime $today): Bill
    {
        return Bill::create([
            'charge' => $charge->def->name,
            'amount' => $charge->def->amount,
            'lendable' => $charge->def->lendable,
            'issued_at' => $today,
        ]);
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param DateTime $today
     *
     * @return void
     */
    private function createOnetimeBill(Charge $charge, Member $member, DateTime $today): void
    {
        // Create new bills only for members that have not already paid.
        $existQuery = OnetimeBill::orWhere(fn($qw) => $qw
                ->where('member_id', $member->id)
                ->where('charge_id', $charge->id))
            ->orWhere(fn($qw) => $qw
                ->whereHas('member', fn($qm) =>
                    $qm->where('def_id', $member->def_id))
                ->whereHas('charge', fn($qc) =>
                    $qc->where('def_id', $charge->def_id))
                ->whereHas('bill', fn($qb) => $qb->whereHas('settlement')));
        if($existQuery->exists())
        {
            return;
        }

        $bill = $this->createBill($charge, $today);
        $onetimeBill = new OnetimeBill();
        $onetimeBill->bill()->associate($bill);
        $onetimeBill->charge()->associate($charge);
        $onetimeBill->member()->associate($member);
        $onetimeBill->save();
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Round $round
     * @param DateTime $today
     *
     * @return void
     */
    private function createRoundBill(Charge $charge, Member $member, Round $round, DateTime $today): void
    {
        $existQuery = RoundBill::whereNotNull('id')
            ->where('member_id', $member->id)
            ->where('charge_id', $charge->id);
        if($existQuery->exists())
        {
            return;
        }

        $bill = $this->createBill($charge, $today);
        $roundBill = new RoundBill();
        $roundBill->bill()->associate($bill);
        $roundBill->charge()->associate($charge);
        $roundBill->member()->associate($member);
        $roundBill->round()->associate($round);
        $roundBill->save();
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Session $session
     * @param DateTime $today
     *
     * @return void
     */
    private function createSessionBill(Charge $charge, Member $member, Session $session, DateTime $today): void
    {
        $bill = $this->createBill($charge, $today);
        $sessionBill = new SessionBill();
        $sessionBill->bill()->associate($bill);
        $sessionBill->charge()->associate($charge);
        $sessionBill->member()->associate($member);
        $sessionBill->session()->associate($session);
        $sessionBill->save();
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Round|null $round
     * @param Collection|array $sessions
     * @param DateTime $today
     *
     * @return void
     */
    private function createBills(Charge $charge, Member $member,
        Round|null $round, Collection|array $sessions, DateTime $today): void
    {
        // No bills for fines.
        if($charge->def->is_fine)
        {
            return;
        }
        if($round !== null && $charge->def->period_once)
        {
            $this->createOnetimeBill($charge, $member, $today);
            return;
        }
        if($round !== null && $charge->def->period_round)
        {
            $this->createRoundBill($charge, $member, $round, $today);
            return;
        }
        foreach($sessions as $session)
        {
            $this->createSessionBill($charge, $member, $session, $today);
        }
    }

    /**
     * @param Charge|Member $owner
     *
     * @return void
     */
    private function deleteBills(Charge|Member $owner): void
    {
        // Will fail if a settlement exists for any of those bills.
        $billIds = $owner->onetime_bills()->pluck('bill_id')
            ->concat($owner->round_bills()->pluck('bill_id'))
            ->concat($owner->session_bills()->pluck('bill_id'))
            ->concat($owner->libre_bills()->pluck('bill_id'));
        $owner->onetime_bills()->delete();
        $owner->round_bills()->delete();
        $owner->session_bills()->delete();
        $owner->libre_bills()->delete();
        DB::table('bills')->whereIn('id', $billIds)->delete();
    }

    /**
     * @param Round $round
     * @param Charge $charge
     *
     * @return void
     */
    public function chargeEnabled(Round $round, Charge $charge): void
    {
        $today = now();
        foreach($round->members as $member)
        {
            $this->createBills($charge, $member, $round, $round->sessions, $today);
        }
    }

    /**
     * @param Round $round
     * @param Charge $charge
     *
     * @return void
     */
    public function chargeRemoved(Round $round, Charge $charge): void
    {
        $this->deleteBills($charge);
    }

    /**
     * @param Round $round
     * @param Member $member
     *
     * @return void
     */
    public function memberEnabled(Round $round, Member $member): void
    {
        $today = now();
        foreach($round->charges as $charge)
        {
            $this->createBills($charge, $member, $round, $round->sessions, $today);
        }
    }

    /**
     * @param Round $round
     * @param Member $member
     *
     * @return void
     */
    public function memberRemoved(Round $round, Member $member): void
    {
        $this->deleteBills($member);
    }

    /**
     * @param Round $round
     * @param Collection|array $sessions
     *
     * @return void
     */
    public function sessionsCreated(Round $round, Collection|array $sessions): void
    {
        $today = now();
        foreach($round->charges as $charge)
        {
            foreach($round->members as $member)
            {
                $this->createBills($charge, $member, $round, $sessions, $today);
            }
        }
    }

    /**
     * @param Session $session
     *
     * @return void
     */
    public function sessionDeleted(Session $session)
    {
        // Will fail if a settlement exists for any of those bills.
        $billIds = $session->session_bills()->pluck('bill_id')
            ->concat($session->libre_bills()->pluck('bill_id'));
        $session->session_bills()->delete();
        $session->libre_bills()->delete();
        DB::table('bills')->whereIn('id', $billIds)->delete();
    }
}
