<?php

namespace Siak\Tontine\Service\Events;

use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\TontineBill;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\SessionBill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;

use function now;

trait EventTrait
{
    /**
     * @param Tontine $tontine
     * @param Charge $charge
     *
     * @return void
     */
    protected function chargeCreated(Tontine $tontine, Charge $charge)
    {
        if($charge->is_fine || !$charge->period_once)
        {
            return;
        }
        DB::transaction(function() {
            $today = now();
            // Create a tontine bill for each member
            foreach($tontine->members()->get() as $member)
            {
                $bill = new Bill();
                $bill->charge = $charge->name;
                $bill->amount = $charge->amount;
                $bill->issued_at = $today;
                $bill->save();
                $tontineBill = new TontineBill();
                $tontineBill->bill()->associate($bill);
                $tontineBill->charge()->associate($charge);
                $tontineBill->member()->associate($member);
                $tontineBill->save();
            }
        });
    }

    /**
     * @param Tontine $tontine
     * @param Member $member
     *
     * @return void
     */
    protected function memberCreated(Tontine $tontine, Member $member)
    {
        DB::transaction(function() {
            $today = now();
            // Create a tontine bill for each charge
            foreach($tontine->charges()->once()->get() as $charge)
            {
                $bill = new Bill();
                $bill->charge = $charge->name;
                $bill->amount = $charge->amount;
                $bill->issued_at = $today;
                $bill->save();
                $tontineBill = new TontineBill();
                $tontineBill->bill()->associate($bill);
                $tontineBill->charge()->associate($charge);
                $tontineBill->member()->associate($member);
                $tontineBill->save();
            }
        });
    }

    /**
     * @param Tontine $tontine
     * @param Round $round
     *
     * @return void
     */
    protected function roundOpened(Tontine $tontine, Round $round)
    {
        DB::transaction(function() {
            $today = now();
            foreach($tontine->charges()->round()->get() as $charge)
            {
                // Create a round bill for each member
                foreach($tontine->members()->get() as $member)
                {
                    $bill = new Bill();
                    $bill->charge = $charge->name;
                    $bill->amount = $charge->amount;
                    $bill->issued_at = $today;
                    $bill->save();
                    $roundBill = new RoundBill();
                    $roundBill->bill()->associate($bill);
                    $roundBill->charge()->associate($charge);
                    $roundBill->member()->associate($member);
                    $roundBill->round()->associate($round);
                    $roundBill->save();
                }
            };
        });
    }

    /**
     * @param Tontine $tontine
     * @param Session $session
     *
     * @return void
     */
    protected function sessionOpened(Tontine $tontine, Session $session)
    {
        DB::transaction(function() {
            $today = now();
            foreach($tontine->charges()->session()->get() as $charge)
            {
                // Create a session bill for each member
                foreach($tontine->members()->get() as $member)
                {
                    $bill = new Bill();
                    $bill->charge = $charge->name;
                    $bill->amount = $charge->amount;
                    $bill->issued_at = $today;
                    $bill->save();
                    $sessionBill = new SessionBill();
                    $sessionBill->bill()->associate($bill);
                    $sessionBill->charge()->associate($charge);
                    $sessionBill->member()->associate($member);
                    $sessionBill->session()->associate($session);
                    $sessionBill->save();
                }
            };
        });
    }
}
